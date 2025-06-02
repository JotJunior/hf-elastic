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
use Hyperf\Stringable\Str;
use Jot\HfElastic\Exception\DeleteErrorException;
use Throwable;

use function Hyperf\Translation\__;

trait ElasticPersistenceTrait
{
    public function select(array|string $fields = '*'): self
    {
        if (! is_array($fields)) {
            return $this;
        }
        $this->queryContext->setBodyParam('_source', is_array($fields) ? $fields : []);
        return $this;
    }

    public function update(string $id, array $data): array
    {
        $currentVersion = $this->getDocumentVersion($id);

        if (empty($currentVersion)) {
            return [
                'data' => null,
                'result' => 'error',
                'message' => __('hf-elastic.document_not_found'),
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

            $this->client->indices()->refresh(['index' => $this->queryContext->getIndex()]);

            return [
                'data' => $data,
                'result' => $result['result'],
                'message' => null,
            ];
        } catch (Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'message' => __('hf-elastic.error_occurred', ['message' => $this->parseError($e)]),
            ];
        }
    }

    /**
     * Verifica se um documento possui referências em outros índices antes de ser excluído.
     *
     * @param string $id ID do documento a ser verificado
     * @param string $prefix Prefixo dos índices a serem pesquisados (padrão: 'prfx_')
     * @return array Lista com os nomes dos índices e IDs dos registros que referenciam o documento
     */
    public function checkReferences(string $id, string $prefix): array
    {
        try {
            $indicesResponse = $this->client->indices()->get([
                'index' => $prefix . '*',
            ]);

            $indices = array_keys($indicesResponse);
            $currentIndex = $this->queryContext->getIndex();
            $references = [];

            $indices = array_filter($indices, function ($index) use ($currentIndex) {
                return $index !== $currentIndex;
            });

            if (empty($indices)) {
                return $references;
            }

            $searchResponse = $this->client->search([
                'index' => implode(',', $indices),
                'body' => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'term' => [
                                        'field.id' => $id,
                                    ],
                                ],
                                [
                                    'term' => [
                                        'deleted' => false,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'size' => 100,
                    '_source' => ['id'],
                ],
            ]);

            if (isset($searchResponse['hits']['hits']) && count($searchResponse['hits']['hits']) > 0) {
                foreach ($searchResponse['hits']['hits'] as $hit) {
                    $references[] = [
                        'index' => $hit['_index'],
                        'id' => $hit['_source']['id'] ?? $hit['_id'],
                    ];
                }
            }

            return $references;
        } catch (Throwable $e) {
            return [];
        }
    }

    public function delete(string $id, bool $logicalDeletion = true): array
    {
        $currentVersion = $this->getDocumentVersion($id);

        if (empty($currentVersion)) {
            return [
                'result' => 'error',
                'message' => __('hf-elastic.document_not_found'),
                'data' => [],
            ];
        }

        $prefix = explode('_', $this->queryContext->getIndex())[0];
        $references = $this->checkReferences($id, $prefix);
        if (! empty($references)) {
            return [
                'result' => 'error',
                'message' => __('hf-elastic.cannot_delete_referenced_document'),
                'data' => [
                    'references' => $references,
                ],
            ];
        }

        if ($logicalDeletion) {
            $data = ['deleted' => true];
            return $this->update($id, $data);
        }

        try {
            $result = $this->client->delete([
                'index' => $this->queryContext->getIndex(),
                'id' => $id,
            ]);

            $this->client->indices()->refresh(['index' => $this->queryContext->getIndex()]);

            return [
                'data' => null,
                'result' => $result['result'],
                'message' => null,
            ];
        } catch (Throwable $e) {
            throw new DeleteErrorException(__('hf-elastic.error_occurred', ['message' => $e->getMessage()]));
        }
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

            $this->client->indices()->refresh(['index' => $this->queryContext->getIndex()]);

            return [
                'data' => $data,
                'result' => $result['result'],
                'message' => null,
            ];
        } catch (Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'message' => __('hf-elastic.error_occurred', ['message' => $this->parseError($e)]),
            ];
        }
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
                'message' => null,
            ];
        } catch (Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'message' => __('hf-elastic.error_occurred', ['message' => $this->parseError($e)]),
            ];
        }
    }
}
