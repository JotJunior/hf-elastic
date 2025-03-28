<?php

declare(strict_types=1);

namespace Jot\HfElastic\Query;

use Elasticsearch\Client;
use InvalidArgumentException;
use Jot\HfElastic\ClientBuilder;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Contracts\QueryPersistenceInterface;
use function Hyperf\Translation\__;
use Jot\HfElastic\Services\IndexNameFormatter;
use Throwable;
use function Hyperf\Support\make;

/**
 * Implementation of the QueryBuilderInterface for building Elasticsearch queries.
 */
class ElasticQueryBuilder implements QueryBuilderInterface, QueryPersistenceInterface
{
    use ElasticPersistenceTrait;

    protected const VERSION_FIELD = '@version';
    protected const TIMESTAMP_FIELD = '@timestamp';

    /**
     * @var array Parameters to ignore when performing count operations.
     */
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

    protected Client $client;

    /**
     * @param Client $client The Elasticsearch client.
     * @param IndexNameFormatter $indexFormatter Service for formatting index names.
     * @param OperatorRegistry $operatorRegistry Registry of operator strategies.
     * @param QueryContext $queryContext The query context to build upon.
     */
    public function __construct(
        ClientBuilder                       $clientBuilder,
        protected IndexNameFormatter        $indexFormatter,
        protected readonly OperatorRegistry $operatorRegistry,
        protected readonly QueryContext     $queryContext
    )
    {
        $this->client = $clientBuilder->build();
    }

    public function withSuffix(string $suffix): self
    {
        $this->indexFormatter->setIndexSuffix($suffix);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function into(string $index): self
    {
        return $this->from($index);
    }

    /**
     * {@inheritdoc}
     */
    public function from(string $index): self
    {
        $formattedIndex = $this->indexFormatter->format($index);
        $this->queryContext->setIndex($formattedIndex);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function join(string|array $index): self
    {
        $indices = is_array($index) ? $index : [$index];
        $this->queryContext->setAdditionalIndices($indices);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function andWhere(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self
    {
        return $this->where($field, $operator, $value, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function where(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self
    {
        // Handle cases where the operator is directly a value
        if (is_null($value)) {
            $value = $operator;
            $this->queryContext->addCondition(['term' => [$field => $value]], $context);
            return $this;
        }

        // Find and apply the appropriate operator strategy
        $strategy = $this->operatorRegistry->findStrategy($operator);

        if ($strategy) {
            $condition = $strategy->apply($field, $value, $context);
            $this->queryContext->addCondition($condition, $context);
            return $this;
        }

        throw new InvalidArgumentException(__('messages.hf_elastic.unsupported_operator', ['operator' => $operator]));
    }

    /**
     * {@inheritdoc}
     */
    public function orWhere(string $field, mixed $operator, mixed $value = null, string $subContext = 'should'): self
    {
        return $this->where($field, $operator, $value, $subContext);
    }

    /**
     * {@inheritdoc}
     */
    public function whereMust(callable $callback): self
    {
        $subQuery = make(self::class);
        $callback($subQuery);
        $this->queryContext->addCondition(['bool' => $subQuery->queryContext->getQuery()['bool']], 'must');
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function whereShould(callable $callback): self
    {
        $subQuery = make(self::class);
        $callback($subQuery);
        $this->queryContext->addCondition(
            ['bool' => ['should' => $subQuery->queryContext->getQuery()['bool']['must'] ?? []]],
            'must'
        );
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function whereNested(string $path, callable $callback): self
    {
        $subQuery = make(self::class);
        $callback($subQuery);
        $this->queryContext->addCondition(
            ['nested' => ['path' => $path, 'query' => $subQuery->queryContext->getQuery()]],
            'must'
        );
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function limit(int $limit): self
    {
        $this->queryContext->setBodyParam('size', $limit);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offset(int $offset): self
    {
        $this->queryContext->setBodyParam('from', $offset);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy(string $field, string $order = 'asc'): self
    {
        $body = $this->queryContext->getBody();
        if (!isset($body['sort'])) {
            $body['sort'] = [];
        }
        $body['sort'][] = [$field => $order];
        $this->queryContext->setBodyParam('sort', $body['sort']);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function geoDistance(string $field, string $location, string $distance): self
    {
        $this->queryContext->addCondition(
            ['geo_distance' => ['distance' => $distance, 'location' => $location]],
            'must'
        );
        return $this;
    }

    /**
     * {@inheritdoc}
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

        $this->queryContext->reset();
        return $result['count'];
    }

    public function toArray(): array
    {
        return $this->queryContext->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentVersion(string $id): ?int
    {
        $result = $this->select([self::VERSION_FIELD])
            ->from($this->queryContext->getIndex())
            ->where('id', '=', $id)
            ->where('deleted', '=', false)
            ->execute();

        return $result['data'][0][self::VERSION_FIELD] ?? null;
    }

    public function info(): array
    {
        return $this->client->info();
    }

    /**
     * Parses an exception to extract a meaningful error message.
     * @param Throwable $exception The exception to parse.
     * @return string The parsed error message.
     */
    protected function parseError(Throwable $exception): string
    {
        $errorDetails = json_decode($exception->getMessage(), true);
        $message = __('messages.hf_elastic.invalid_query') . ': ' . $exception->getMessage();

        if (json_last_error() === JSON_ERROR_NONE && isset($errorDetails['error']['reason'])) {
            $message = $errorDetails['error']['root_cause'][0]['reason'] ?? $errorDetails['error']['reason'];
        }

        return $message;
    }


}
