<?php

namespace Jot\HfElastic\Query;

use Hyperf\Stringable\Str;
use Jot\HfElastic\Exception\DeleteErrorException;
use Throwable;

trait ElasticPersistenceTrait
{

    /**
     * {@inheritdoc}
     */
    public function select(string|array $fields = '*'): self
    {
        $this->queryContext->setBodyParam('_source', is_array($fields) ? $fields : []);
        return $this;
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
                'index' => $this->queryContext->getIndex(),
                'id' => $id,
            ]);
            return [
                'data' => null,
                'result' => $result['result'],
                'error' => null,
            ];
        } catch (Throwable $e) {
            throw new DeleteErrorException($e->getMessage());
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

        unset($data[self::TIMESTAMP_FIELD]);
        unset($data[self::VERSION_FIELD]);
        $data[self::VERSION_FIELD] = ++$currentVersion;
        $data['updated_at'] = (new DateTime('now'))->format(DATE_ATOM);

        try {
            $result = $this->client->update([
                'index' => $this->queryContext->getIndex(),
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
    public function insert(array $data): array
    {
        if (!empty($data['id']) && $this->getDocumentVersion($data['id'])) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => sprintf('Document with id %s already exists.', $data['id']),
            ];
        }

        $createdAt = DateTime::createFromFormat('U.u', microtime(true))->format('Y-m-d\TH:i:s.u\Z');

        $data = [
            ...$data,
            'created_at' => $createdAt,
            'updated_at' => null,
            'deleted' => false,
            self::TIMESTAMP_FIELD => $createdAt,
            self::VERSION_FIELD => 1,
        ];

        try {
            $data['id'] = $data['id'] ?? Str::uuid()->toString();
            $result = $this->client->create([
                'index' => $this->queryContext->getIndex(),
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
    public function execute(): array
    {
        $query = $this->toArray();

        try {
            $result = $this->client->search([
                'index' => $query['index'],
                'body' => $query['body'],
            ]);

            $this->queryContext->reset();
            return [
                'data' => array_map(fn($hit) => $hit['_source'], $result['hits']['hits']),
                'result' => 'success',
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


}
