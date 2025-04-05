<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Query;

use DateTime;
use Hyperf\Coroutine\Coroutine;
use Hyperf\Stringable\Str;
use Jot\HfElastic\Exception\DeleteErrorException;
use Throwable;

use function Hyperf\Translation\__;

trait ElasticPersistenceTrait
{
    public function select(array|string $fields = '*'): self
    {
        $this->queryContext->setBodyParam('_source', is_array($fields) ? $fields : []);
        return $this;
    }

    /**
     * Asynchronous version of delete method for use with coroutines in Hyperf 3.1.
     * @param string $id The document ID to be deleted
     * @param bool $logicalDeletion If true, performs logical deletion; otherwise, physical deletion
     */
    public function deleteAsync(string $id, bool $logicalDeletion = true): int
    {
        return Coroutine::create(function () use ($id, $logicalDeletion) {
            $currentVersion = $this->getDocumentVersion($id);

            if ($logicalDeletion) {
                if (empty($currentVersion)) {
                    return [
                        'result' => 'error',
                        'error' => 'Document not found',
                        'data' => [],
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
                return [
                    'data' => null,
                    'result' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        });
    }

    public function update(string $id, array $data): array
    {
        $currentVersion = $this->getDocumentVersion($id);

        if (empty($currentVersion)) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => __('hf-elastic.document_not_found'),
            ];
        }

        unset($data[self::TIMESTAMP_FIELD], $data[self::VERSION_FIELD]);

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
                'error' => __('hf-elastic.error_occurred', ['message' => $this->parseError($e)]),
            ];
        }
    }

    public function delete(string $id, bool $logicalDeletion = true): array
    {
        $currentVersion = $this->getDocumentVersion($id);

        if ($logicalDeletion) {
            if (empty($currentVersion)) {
                return [
                    'result' => 'error',
                    'error' => __('hf-elastic.document_not_found'),
                    'data' => [],
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
            throw new DeleteErrorException(__('hf-elastic.error_occurred', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Asynchronous version of update method for use with coroutines in Hyperf 3.1.
     * @param string $id The document ID to be updated
     * @param array $data The data to be updated
     */
    public function updateAsync(string $id, array $data): int
    {
        return Coroutine::create(function () use ($id, $data) {
            $currentVersion = $this->getDocumentVersion($id);

            if (empty($currentVersion)) {
                return [
                    'data' => null,
                    'result' => 'error',
                    'error' => __('hf-elastic.document_not_found'),
                ];
            }

            unset($data[self::TIMESTAMP_FIELD], $data[self::VERSION_FIELD]);

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
                    'error' => __('hf-elastic.error_occurred', ['message' => $this->parseError($e)]),
                ];
            }
        });
    }

    public function insert(array $data): array
    {
        $createdAt = (new DateTime('now'))->format('Y-m-d\TH:i:s.u\Z');

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
                'error' => __('hf-elastic.error_occurred', ['message' => $this->parseError($e)]),
            ];
        }
    }

    /**
     * Asynchronous version of insert method for use with coroutines in Hyperf 3.1.
     * @param array $data The data to be inserted
     */
    public function insertAsync(array $data): int
    {
        return Coroutine::create(function () use ($data) {
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
                    'error' => __('hf-elastic.error_occurred', ['message' => $this->parseError($e)]),
                ];
            }
        });
    }

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
                'data' => array_map(fn ($hit) => $hit['_source'], $result['hits']['hits']),
                'result' => 'success',
                'error' => null,
            ];
        } catch (Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => __('hf-elastic.error_occurred', ['message' => $this->parseError($e)]),
            ];
        }
    }

    /**
     * Asynchronous version of execute method for use with coroutines in Hyperf 3.1.
     */
    public function executeAsync(): int
    {
        return Coroutine::create(function () {
            $query = $this->toArray();

            try {
                $result = $this->client->search([
                    'index' => $query['index'],
                    'body' => $query['body'],
                ]);

                $this->queryContext->reset();
                return [
                    'data' => array_map(fn ($hit) => $hit['_source'], $result['hits']['hits']),
                    'result' => 'success',
                    'error' => null,
                ];
            } catch (Throwable $e) {
                return [
                    'data' => null,
                    'result' => 'error',
                    'error' => __('hf-elastic.error_occurred', ['message' => $this->parseError($e)]),
                ];
            }
        });
    }
}
