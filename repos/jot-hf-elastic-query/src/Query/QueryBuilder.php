<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Query;

use InvalidArgumentException;
use Jot\HfElasticCore\Client\ElasticClient;
use Jot\HfElasticQuery\Contracts\OperatorRegistryInterface;
use Jot\HfElasticQuery\Contracts\QueryBuilderInterface;
use Jot\HfElasticQuery\Contracts\QueryContextInterface;
use Jot\HfElasticQuery\Context\QueryContext;

/**
 * Query builder for Elasticsearch.
 */
class QueryBuilder implements QueryBuilderInterface
{
    /**
     * The Elasticsearch client.
     *
     * @var ElasticClient
     */
    protected ElasticClient $client;

    /**
     * The operator registry.
     *
     * @var OperatorRegistryInterface
     */
    protected OperatorRegistryInterface $operatorRegistry;

    /**
     * The query context.
     *
     * @var QueryContextInterface
     */
    protected QueryContextInterface $context;

    /**
     * Constructor.
     *
     * @param ElasticClient $client
     * @param OperatorRegistryInterface $operatorRegistry
     * @param QueryContextInterface|null $context
     */
    public function __construct(
        ElasticClient $client,
        OperatorRegistryInterface $operatorRegistry,
        ?QueryContextInterface $context = null
    ) {
        $this->client = $client;
        $this->operatorRegistry = $operatorRegistry;
        $this->context = $context ?? new QueryContext();
    }

    /**
     * {@inheritdoc}
     */
    public function index(string $index): self
    {
        $this->context->setIndex($index);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function where(string $field, mixed $operator, mixed $value = null): self
    {
        // If only two arguments are provided, use the equals operator
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        if (!$this->operatorRegistry->has($operator)) {
            throw new InvalidArgumentException(sprintf(
                'Operator "%s" is not registered.',
                $operator
            ));
        }

        $operatorInstance = $this->operatorRegistry->get($operator);

        if (!$operatorInstance->supports($value)) {
            throw new InvalidArgumentException(sprintf(
                'Operator "%s" does not support the provided value.',
                $operator
            ));
        }

        $this->context->addFilter($operatorInstance->apply($field, $value));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function whereIn(string $field, array $values): self
    {
        return $this->where($field, 'in', $values);
    }

    /**
     * {@inheritdoc}
     */
    public function whereNotIn(string $field, array $values): self
    {
        return $this->where($field, 'not in', $values);
    }

    /**
     * {@inheritdoc}
     */
    public function whereBetween(string $field, mixed $from, mixed $to): self
    {
        return $this->where($field, 'between', [$from, $to]);
    }

    /**
     * {@inheritdoc}
     */
    public function whereNotBetween(string $field, mixed $from, mixed $to): self
    {
        return $this->where($field, 'not between', [$from, $to]);
    }

    /**
     * {@inheritdoc}
     */
    public function whereNull(string $field): self
    {
        return $this->where($field, 'not exists');
    }

    /**
     * {@inheritdoc}
     */
    public function whereNotNull(string $field): self
    {
        return $this->where($field, 'exists');
    }

    /**
     * {@inheritdoc}
     */
    public function whereExists(string $field): self
    {
        return $this->where($field, 'exists');
    }

    /**
     * {@inheritdoc}
     */
    public function whereNotExists(string $field): self
    {
        return $this->where($field, 'not exists');
    }

    /**
     * {@inheritdoc}
     */
    public function search(string|array $fields, string $query, array $options = []): self
    {
        $defaultOptions = [
            'fuzziness' => 'AUTO',
            'operator' => 'or',
            'boost' => 1.0,
        ];

        $options = array_merge($defaultOptions, $options);

        $matchQuery = [
            'query' => $query,
            'fuzziness' => $options['fuzziness'],
            'operator' => $options['operator'],
            'boost' => $options['boost'],
        ];

        if (is_array($fields)) {
            $this->context->addMust([
                'multi_match' => array_merge(
                    ['fields' => $fields],
                    $matchQuery
                ),
            ]);
        } else {
            $this->context->addMust([
                'match' => [
                    $fields => $matchQuery,
                ],
            ]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy(string $field, string $direction = 'asc'): self
    {
        $direction = strtolower($direction);

        if (!in_array($direction, ['asc', 'desc'])) {
            throw new InvalidArgumentException(
                'Direction must be "asc" or "desc".',
            );
        }

        $this->context->addSort($field, $direction);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function limit(int $limit): self
    {
        $this->context->setSize($limit);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offset(int $offset): self
    {
        $this->context->setFrom($offset);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function aggregate(string $name, array $aggregation): self
    {
        $this->context->addAggregation($name, $aggregation);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get(): array
    {
        $params = $this->context->build();

        $result = $this->client->search($params);

        return $this->formatResult($result);
    }

    /**
     * {@inheritdoc}
     */
    public function first(): ?array
    {
        $this->limit(1);

        $result = $this->get();

        return $result['hits'][0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        $params = $this->context->build();

        // Override size to 0 for count queries
        $params['body']['size'] = 0;

        $result = $this->client->count($params);

        return $result['count'] ?? 0;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage = 15, int $page = 1): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $this->limit($perPage)->offset($offset);

        $result = $this->get();
        $total = $result['total'] ?? 0;

        return [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
            'data' => $result['hits'] ?? [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->context->build();
    }

    /**
     * Format the search result.
     *
     * @param array $result
     * @return array
     */
    protected function formatResult(array $result): array
    {
        $hits = [];
        $total = 0;

        if (isset($result['hits'])) {
            if (isset($result['hits']['total'])) {
                if (is_array($result['hits']['total'])) {
                    $total = $result['hits']['total']['value'] ?? 0;
                } else {
                    $total = $result['hits']['total'] ?? 0;
                }
            }

            if (isset($result['hits']['hits']) && is_array($result['hits']['hits'])) {
                foreach ($result['hits']['hits'] as $hit) {
                    $document = $hit['_source'] ?? [];
                    $document['_id'] = $hit['_id'] ?? null;
                    $document['_score'] = $hit['_score'] ?? null;

                    if (isset($hit['highlight'])) {
                        $document['highlight'] = $hit['highlight'];
                    }

                    $hits[] = $document;
                }
            }
        }

        $formatted = [
            'total' => $total,
            'hits' => $hits,
        ];

        if (isset($result['aggregations'])) {
            $formatted['aggregations'] = $result['aggregations'];
        }

        return $formatted;
    }
}
