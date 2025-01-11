<?php

/**
 * Class QueryBuilder
 * Provides a fluent interface to build complex Elasticsearch queries.
 */

namespace Jot\HfElastic;

use Elasticsearch\Client;
use Hyperf\Stringable\Str;
use Jot\HfElastic\Exception\DocumentExistsException;
use Jot\HfElastic\Migration\ElasticsearchType\DateType;
use Psr\Container\ContainerInterface;
use stdClass;

class QueryBuilder
{

    protected string $index;
    protected Client $client;
    protected array $body = [];
    protected array $query = [];
    protected array $aggs = [];
    protected array $ignoredParamsForCount = [
        '_source',
        'sort',
        'size',
        'from',
        'aggs',
        'scroll',
        'terminate_after',
        'track_total_hits',
        'track_scores',
        'version',
        'explain'
    ];

    public function __construct(ContainerInterface $container)
    {
        $this->client = $container->get(ClientBuilder::class)->build();
    }

    public function select(string|array $fields = '*'): self
    {
        $this->body['_source'] = is_array($fields) ? $fields : [];
        return $this;
    }

    public function from(string $index): self
    {
        $this->index = $index;
        return $this;
    }

    public function where(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self
    {
        // Ensure the given context (must, must_not, should) exists within the bool query
        if (!isset($this->query['bool'][$context])) {
            $this->query['bool'][$context] = [];
        }

        // Handle cases where the operator is directly a value (e.g., `where('field', 'value')`)
        if (is_null($value)) {
            $value = $operator;
            $this->query['bool'][$context][] = ['term' => [$field => $value]];
            return $this;
        }

        // Handle cases with specific operators like '=', '!=', '>', '<', '>=', '<=', etc.
        switch ($operator) {
            case '=':
                $this->query['bool'][$context][] = ['term' => [$field => $value]];
                break;

            case '!=':
                if ($context === 'must') {
                    $this->query['bool']['must_not'][] = ['term' => [$field => $value]];
                } else {
                    $this->query['bool'][$context][] = ['bool' => ['must_not' => [['term' => [$field => $value]]]]];
                }
                break;

            case '>':
                $this->query['bool'][$context][] = ['range' => [$field => ['gt' => $value]]];
                break;

            case '<':
                $this->query['bool'][$context][] = ['range' => [$field => ['lt' => $value]]];
                break;

            case '>=':
                $this->query['bool'][$context][] = ['range' => [$field => ['gte' => $value]]];
                break;

            case '<=':
                $this->query['bool'][$context][] = ['range' => [$field => ['lte' => $value]]];
                break;

            case 'exists':
                $this->query['bool'][$context][] = ['exists' => ['field' => $value]];
                break;

            case 'distance':
                $this->query['bool'][$context][] = ['geo_distance' => ['distance' => $value['distance'], $field => ['lat' => $value['lat'], 'lon' => $value['lon']]]];
                break;

            case 'between':
                $this->query['bool'][$context][] = ['range' => [$field => ['gte' => $value[0], 'lte' => $value[1]]]];
                break;

            case 'like':
                $this->query['bool'][$context][] = ['wildcard' => [$field => str_replace('%', '*', $value)]];
                break;

            default:
                throw new \InvalidArgumentException("Unsupported operator: {$operator}");
        }

        return $this;
    }

    public function andWhere(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self
    {
        return $this->where($field, $operator, $value, $context);
    }

    public function orWhere(string $field, mixed $operator, mixed $value = null, string $subContext = 'should'): self
    {
        // Ensure the top-level bool query and the should context exist
        if (!isset($this->query['bool'][$subContext])) {
            $this->query['bool'][$subContext] = [];
        }

        // Handle cases where the operator is directly a value (e.g., `orWhere('field', 'value')`)
        if (is_null($value)) {
            $value = $operator;
            $this->query['bool'][$subContext][] = ['term' => [$field => $value]];
            return $this;
        }

        // Handle cases with specific operators like '=', '!=', '>', '<', '>=', '<=', etc.
        switch ($operator) {
            case '=':
                $this->query['bool'][$subContext][] = ['term' => [$field => $value]];
                break;

            case '!=':
                $this->query['bool'][$subContext][] = ['bool' => ['must_not' => [['term' => [$field => $value]]]]];
                break;

            case '>':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['gt' => $value]]];
                break;

            case '<':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['lt' => $value]]];
                break;

            case '>=':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['gte' => $value]]];
                break;

            case '<=':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['lte' => $value]]];
                break;

            case 'exists':
                $this->query['bool'][$subContext][] = ['exists' => ['field' => $value]];
                break;

            case 'distance':
                $this->query['bool'][$subContext][] = ['geo_distance' => ['distance' => $value['distance'], $field => ['lat' => $value['lat'], 'lon' => $value['lon']]]];
                break;

            case 'between':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['gte' => $value[0], 'lte' => $value[1]]]];
                break;

            case 'like':
                $this->query['bool'][$subContext][] = ['wildcard' => [$field => str_replace('%', '*', $value)]];
                break;

            default:
                throw new \InvalidArgumentException("Unsupported operator: {$operator}");
        }

        return $this;
    }

    public function whereMust(callable $callback): self
    {
        $subQuery = new self();
        $callback($subQuery);
        $this->query['bool']['must'][] = ['bool' => $subQuery->query['bool']];
        return $this;
    }

    public function whereShould(callable $callback): self
    {
        $subQuery = new self();
        $callback($subQuery);
        $this->query['bool']['must'][] = ['bool' => ['should' => $subQuery->query['bool']['must']]];
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->body['size'] = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->body['from'] = $offset;
        return $this;
    }

    public function whereNested(string $path, callable $callback): self
    {
        $subQuery = new self();
        $callback($subQuery);
        $this->query['bool']['must'][] = ['nested' => ['path' => $path, 'query' => $subQuery->query]];
        return $this;
    }

    public function orderBy(string $field, string $order = 'asc'): self
    {
        $this->body['sort'][] = [$field => $order];
        return $this;
    }

    public function geoDistance(string $field, string $location, string $distance): self
    {
        $this->query['bool']['must'][] = ['geo_distance' => ['distance' => $distance, 'location' => $location]];
        return $this;
    }

    public function toArray(): array
    {
        if (empty($this->query)) {
            $this->query = ['match_all' => new stdClass()];
        }
        $query = [
            'index' => $this->index,
            'body' => [
                ...$this->body,
                'query' => $this->query
            ]
        ];

        if (!empty($this->aggs)) {
            $query['body']['aggs'] = $this->aggs;
        }

        return $query;
    }


    /**
     * Executes a search query on the specified index and retrieves matching results.
     *
     * @return array Returns an array of search hits retrieved from the query execution.
     */
    public function execute(): array
    {
        $query = $this->toArray();
        $result = $this->client->search([
            'index' => $query['index'],
            'body' => $query['body'],
        ]);
        return array_values($result['hits']['hits']);
    }

    /**
     * Counts the number of documents in the index matching the specified query.
     *
     * This method sends a count query to Elasticsearch and retrieves the number
     * of documents that match the query parameters provided in the request body.
     *
     * @return int Returns the total number of documents that satisfy the query criteria.
     * @throws \InvalidArgumentException If the query parameters are invalid or not properly defined.
     */
    public function count(): int
    {
        $query = $this->toArray();
        foreach ($this->ignoredParamsForCount as $ignoredParam) {
            unset($query['body'][$ignoredParam]);
        }
        $result = $this->client->count([
            'index' => $query['index'],
            'body' => $query['body'],
        ]);
        return $result['count'];
    }

    /**
     * Creates and indexes a document in the specified index. If an ID is provided and already exists, an exception is thrown.
     *
     * @param array $data The data to be indexed. Should include an 'id' field if a specific ID is desired.
     * @return array Returns an array containing the result of the indexing operation and the original data.
     * @throws DocumentExistsException If a document with the provided ID already exists in the index.
     */
    public function create(array $data): array
    {
        if (!empty($data['id']) && $this->client->exists(['index' => $this->index, 'id' => $data['id']])) {
            throw new DocumentExistsException();
        }
        $result = $this->client->index([
            'index' => $this->index,
            'id' => $data['id'] ?? Str::uuid(),
            'body' => $data
        ]);

        return [
            'result' => $result['result'],
            'data' => $data
        ];

    }


    /**
     * Updates an existing document in the specified index by merging new data with the current document data.
     *
     * @param string $id The ID of the document to update.
     * @param array $data The new data to merge with the existing document.
     * @return array Returns an array containing the result of the update operation and the merged document data.
     */
    public function update(string $id, array $data): array
    {
        $existingDocument = $this->client->get([
            'index' => $this->index,
            'id' => $id,
        ]);

        $updatedData = array_replace_recursive($existingDocument['_source'], $data);

        $result = $this->client->index([
            'index' => $this->index,
            'id' => $id,
            'body' => $updatedData,
        ]);

        return [
            'result' => $result['result'],
            'data' => $updatedData,
        ];
    }

    /**
     * Deletes or updates a single document in the index.
     *
     * This method performs a deletion operation on a specific document based on its ID.
     * It supports logical deletion by updating the document with specified attributes
     * or physical deletion by removing the document from the index.
     *
     * @param string $id The ID of the document to be deleted or updated.
     * @param bool $logicalDeletion A flag indicating whether to perform a logical deletion (update the document)
     *                               or a physical deletion (remove the document). Default is true (logical deletion).
     * @return string Returns the result of the operation, which could be 'updated' for logical deletion
     *                or 'deleted' for physical deletion.
     */
    public function delete(string $id, bool $logicalDeletion = true): string
    {
        if ($logicalDeletion) {
            return $this->update($id, ['removed' => true])['result'];
        }
        return $this->client->delete([
            'index' => $this->index,
            'id' => $id,
        ])['result'];
    }


    /**
     * Performs a bulk update operation on documents in the database based on the provided query and data.
     * Retrieves documents matching the query in batches, updates their content, and writes the changes back in bulk.
     *
     * @param array $data An array of key-value pairs representing the data to update in the matched documents.
     *                    The provided data will be merged with the existing document content.
     * @return array An associative array containing the count of updated documents ('updated_count') and an array
     *               of document IDs that were updated ('updated_ids').
     *
     * @throws \InvalidArgumentException If the query parameters are missing or invalid.
     */
    public function bulkUpdate(array $data): array
    {
        $query = $this->toArray();
        if (empty($query['body']['query']) || $query['body']['query'] = ['match_all' => new stdClass()]) {
            throw new \InvalidArgumentException("At least one query parameter is required for bulk update.");
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

                $updatedData = array_replace_recursive($source, $data);
                $updatedData['updated_at'] = (new DateType('now'))->format(DATE_ATOM);

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
     * Deletes or updates documents in bulk based on the provided query and data.
     *
     * This method performs a bulk operation on documents retrieved from an Elasticsearch index
     * based on the query parameters. It supports logical deletion by updating documents
     * with specific attributes or physical deletion by removing documents entirely.
     *
     * @param array $data Data to be merged into the documents during a logical deletion update.
     *                    This data is ignored if logical deletion is set to false.
     * @param bool $logicalDeletion A flag indicating whether to perform a logical deletion (update documents)
     *                               or a physical deletion (delete documents). Default is true (logical deletion).
     * @return array Returns an array containing:
     *               - 'deleted_count' (int): The total number of documents affected by the operation.
     *               - 'deleted_ids' (array): An array of document IDs processed during the operation.
     * @throws \InvalidArgumentException If no query parameters are provided for the operation.
     */
    public function bulkDelete(bool $logicalDeletion = true): array
    {
        $query = $this->toArray();
        if (empty($query['body']['query']) || $query['body']['query'] = ['match_all' => new stdClass()]) {
            throw new \InvalidArgumentException("At least one query parameter is required for bulk update.");
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
                $updatedData['removed'] = true;
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


}