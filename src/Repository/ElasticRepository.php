<?php

declare(strict_types=1);

namespace Jot\HfElastic\Repository;

use DateTime;
use Elasticsearch\Client;
use Hyperf\Stringable\Str;
use InvalidArgumentException;
use Jot\HfElastic\Contracts\ElasticRepositoryInterface;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Factories\QueryBuilderFactory;
use stdClass;
use Throwable;

/**
 * Implementation of the ElasticRepositoryInterface for interacting with Elasticsearch.
 */
class ElasticRepository implements ElasticRepositoryInterface
{
    /**
     * @var string The index to operate on.
     */
    protected string $index;
    
    /**
     * @param Client $client The Elasticsearch client.
     * @param QueryBuilderFactory $queryBuilderFactory Factory for creating query builders.
     */
    public function __construct(
        protected readonly Client $client,
        protected readonly QueryBuilderFactory $queryBuilderFactory
    ) {}
    
    /**
     * Sets the index to operate on.
     *
     * @param string $index The index name.
     * @return self
     */
    public function setIndex(string $index): self
    {
        $this->index = $index;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function insert(array $data): array
    {
        if (!empty($data['id']) && $this->getDocumentVersion($data['id'])) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => sprintf('Document with id %s already exists.', $data['id']),
            ];
        }
        
        $data = [
            ...$data,
            'created_at' => (new DateTime('now'))->format(DATE_ATOM),
            'updated_at' => (new DateTime('now'))->format(DATE_ATOM),
            'deleted' => false,
            '@timestamp' => DateTime::createFromFormat('U.u', microtime(true))->format('Y-m-d\TH:i:s.u\Z'),
            '@version' => 1,
        ];
        
        try {
            $data['id'] = $data['id'] ?? Str::uuid()->toString();
            $result = $this->client->create([
                'index' => $this->index,
                'id' => $data['id'],
                'body' => $data,
            ]);
            
            return [
                'data' => $data,
                'result' => $result['result'],
                'error' => null,
            ];
        } catch (Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => $this->parseError($e)
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function update(string $id, array $data): array
    {
        $currentVersion = $this->getDocumentVersion($id);
        
        if (empty($currentVersion)) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => 'Document not found',
            ];
        }
        
        unset($data['@timestamp']);
        unset($data['@version']);
        $data['@version'] = ++$currentVersion;
        $data['updated_at'] = (new DateTime('now'))->format(DATE_ATOM);
        
        try {
            $result = $this->client->update([
                'index' => $this->index,
                'id' => $id,
                'body' => [
                    'doc' => $data,
                ],
            ]);
            
            return [
                'data' => $data,
                'result' => $result['result'],
                'error' => null,
            ];
        } catch (Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => $this->parseError($e),
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete(string $id, bool $logicalDeletion = true): array
    {
        $currentVersion = $this->getDocumentVersion($id);
        
        if ($logicalDeletion) {
            if (empty($currentVersion)) {
                return [
                    'result' => 'error',
                    'error' => 'Document not found',
                    'data' => []
                ];
            }
            $data = ['deleted' => true];
            return $this->update($id, $data);
        }
        
        try {
            $result = $this->client->delete([
                'index' => $this->index,
                'id' => $id,
            ]);
            return [
                'data' => null,
                'result' => $result['result'],
                'error' => null,
            ];
        } catch (Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => $this->parseError($e),
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function bulkUpdate(array $data): array
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->from($this->index);
        $query = $queryBuilder->toArray();
        
        if (empty($query['body']['query']) || $query['body']['query'] === ['match_all' => new stdClass()]) {
            throw new InvalidArgumentException("At least one query parameter is required for bulk update.");
        }
        
        $updatedDocuments = [];
        $scrollTimeout = '1m';
        
        $result = $this->client->search([
            'index' => $this->index,
            'body' => $query['body'],
            'scroll' => $scrollTimeout,
            'size' => 100,
        ]);
        
        $scrollId = $result['_scroll_id'];
        $hits = $result['hits']['hits'];
        
        while (!empty($hits)) {
            $bulkBody = [];
            
            foreach ($hits as $hit) {
                $docId = $hit['_id'];
                $source = $hit['_source'];
                
                unset($data['@timestamp']);
                
                $updatedData = array_replace_recursive($source, $data);
                $updatedData['updated_at'] = (new DateTime('now'))->format(DATE_ATOM);
                $updatedData['@version'] = (int)$source['@version'] + 1;
                
                $bulkBody[] = [
                    'update' => [
                        '_index' => $this->index,
                        '_id' => $docId,
                    ],
                ];
                $bulkBody[] = ['doc' => $updatedData];
                
                $updatedDocuments[] = $docId;
            }
            
            $this->client->bulk(['body' => $bulkBody]);
            
            $scrollResult = $this->client->scroll([
                'scroll_id' => $scrollId,
                'scroll' => $scrollTimeout,
            ]);
            
            $scrollId = $scrollResult['_scroll_id'];
            $hits = $scrollResult['hits']['hits'];
        }
        
        $this->client->clearScroll(['scroll_id' => $scrollId]);
        
        return [
            'updated_count' => count($updatedDocuments),
            'updated_ids' => $updatedDocuments,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function bulkDelete(bool $logicalDeletion = true): array
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->from($this->index);
        $query = $queryBuilder->toArray();
        
        if (empty($query['body']['query']) || $query['body']['query'] === ['match_all' => new stdClass()]) {
            throw new InvalidArgumentException("At least one query parameter is required for bulk delete.");
        }
        
        $updatedDocuments = [];
        $scrollTimeout = '1m';
        
        $result = $this->client->search([
            'index' => $this->index,
            'body' => $query['body'],
            'scroll' => $scrollTimeout,
            'size' => 100,
        ]);
        
        $scrollId = $result['_scroll_id'];
        $hits = $result['hits']['hits'];
        
        while (!empty($hits)) {
            $bulkBody = [];
            
            foreach ($hits as $hit) {
                $docId = $hit['_id'];
                $updatedData['deleted'] = true;
                $updatedData['updated_at'] = (new DateTime('now'))->format(DATE_ATOM);
                $updatedData['@version'] = (int)$hit['_source']['@version'] + 1;
                
                $bulkBody[] = [
                    $logicalDeletion ? 'update' : 'delete' => [
                        '_index' => $this->index,
                        '_id' => $docId,
                    ],
                ];
                
                if ($logicalDeletion) {
                    $bulkBody[] = ['doc' => $updatedData];
                }
                
                $updatedDocuments[] = $docId;
            }
            
            $this->client->bulk(['body' => $bulkBody]);
            
            $scrollResult = $this->client->scroll([
                'scroll_id' => $scrollId,
                'scroll' => $scrollTimeout,
            ]);
            
            $scrollId = $scrollResult['_scroll_id'];
            $hits = $scrollResult['hits']['hits'];
        }
        
        $this->client->clearScroll(['scroll_id' => $scrollId]);
        
        return [
            'deleted_count' => count($updatedDocuments),
            'deleted_ids' => $updatedDocuments,
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDocumentVersion(string $id): ?int
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $result = $queryBuilder->select(['@version'])
            ->from($this->index)
            ->where('id', '=', $id)
            ->where('deleted', '=', false)
            ->execute();
        
        return $result['data'][0]['@version'] ?? null;
    }
    
    /**
     * Parses an exception to extract a meaningful error message.
     *
     * @param Throwable $exception The exception to parse.
     * @return string The parsed error message.
     */
    protected function parseError(Throwable $exception): string
    {
        $errorDetails = json_decode($exception->getMessage(), true);
        $message = 'Invalid query parameters.';
        
        if (json_last_error() === JSON_ERROR_NONE && isset($errorDetails['error']['reason'])) {
            $message = $errorDetails['error']['root_cause'][0]['reason'] ?? $errorDetails['error']['reason'];
        }
        
        return $message;
    }
}
