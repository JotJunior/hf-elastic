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

use Elasticsearch\Client;
use InvalidArgumentException;
use Jot\HfElastic\ClientBuilder;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Contracts\QueryPersistenceInterface;
use Jot\HfElastic\Services\IndexNameFormatter;
use Throwable;

use function Hyperf\Support\make;
use function Hyperf\Translation\__;

/**
 * Implementation of the QueryBuilderInterface for building Elasticsearch queries.
 */
class ElasticQueryBuilder implements QueryBuilderInterface, QueryPersistenceInterface
{
    use ElasticPersistenceTrait;

    protected const VERSION_FIELD = '@version';

    protected const TIMESTAMP_FIELD = '@timestamp';

    /**
     * @var array parameters to ignore when performing count operations
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
        'explain',
    ];

    protected Client $client;

    /**
     * @param IndexNameFormatter $indexFormatter service for formatting index names
     * @param OperatorRegistry $operatorRegistry registry of operator strategies
     * @param QueryContext $queryContext the query context to build upon
     */
    public function __construct(
        ClientBuilder $clientBuilder,
        protected IndexNameFormatter $indexFormatter,
        protected readonly OperatorRegistry $operatorRegistry,
        protected readonly QueryContext $queryContext
    ) {
        $this->client = $clientBuilder->build();
    }

    public function getClient(): Client {
        return $this->client;
    }

    public function withSuffix(string $suffix): self
    {
        $this->indexFormatter->setIndexSuffix($suffix);
        return $this;
    }

    public function into(string $index): self
    {
        return $this->from($index);
    }

    public function from(string $index): self
    {
        $formattedIndex = $this->indexFormatter->format($index);
        $this->queryContext->setIndex($formattedIndex);
        return $this;
    }

    public function join(array|string $index): self
    {
        $indices = is_array($index) ? $index : [$index];
        $this->queryContext->setAdditionalIndices($indices);
        return $this;
    }

    public function andWhere(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self
    {
        return $this->where($field, $operator, $value, $context);
    }

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

        throw new InvalidArgumentException(__('hf-elastic.unsupported_operator', ['operator' => $operator]));
    }

    public function orWhere(string $field, mixed $operator, mixed $value = null, string $subContext = 'should'): self
    {
        return $this->where($field, $operator, $value, $subContext);
    }

    public function whereMust(callable $callback): self
    {
        $subQuery = make(self::class, ['queryContext' => new QueryContext()]);
        $callback($subQuery);
        $this->queryContext->addCondition(['bool' => $subQuery->queryContext->getQuery()['bool']], 'must');
        unset($subQuery);
        return $this;
    }

    public function whereShould(callable $callback): self
    {
        $subQuery = make(self::class, ['queryContext' => new QueryContext()]);
        $callback($subQuery);
        $this->queryContext->addCondition(
            ['bool' => ['should' => $subQuery->queryContext->getQuery()['bool']['must'] ?? []]],
            'must'
        );
        unset($subQuery);
        return $this;
    }

    public function whereNested(string $path, callable $callback): self
    {
        $subQuery = make(self::class, ['queryContext' => new QueryContext()]);
        $callback($subQuery);
        $this->queryContext->addCondition(
            ['nested' => ['path' => $path, 'query' => $subQuery->queryContext->getQuery()]],
            'must'
        );
        unset($subQuery);
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->queryContext->setBodyParam('size', $limit);
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->queryContext->setBodyParam('from', $offset);
        return $this;
    }

    public function orderBy(string $field, string $order = 'asc'): self
    {
        $body = $this->queryContext->getBody();
        if (! isset($body['sort'])) {
            $body['sort'] = [];
        }
        $body['sort'][] = [$field => $order];
        $this->queryContext->setBodyParam('sort', $body['sort']);
        return $this;
    }

    public function geoDistance(string $field, string $location, string $distance): self
    {
        $this->queryContext->addCondition(
            ['geo_distance' => ['distance' => $distance, 'location' => $location]],
            'must'
        );
        return $this;
    }

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

    public function getDocumentVersion(string $id): ?int
    {
        $result = $this->select([self::VERSION_FIELD])
            ->from($this->queryContext->getIndex())
            ->where('id', '=', $id)
            ->where('deleted', '=', false)
            ->execute();

        return $result['data'][0][self::VERSION_FIELD] ?? null;
    }

    /**
     * Checks if a document with the specified ID exists in the index.
     * @param string $id the identifier of the document to check for existence
     * @return bool returns true if the document exists, false otherwise
     */
    public function exists(string $id): bool
    {
        $query = $this->toArray();
        return $this->client->exists([
            'index' => $query['index'],
            'id' => $id,
        ]);
    }

    /**
     * Executes an autocomplete operation based on the provided keyword and searchable fields.
     * @param string $keyword the input keyword to generate autocomplete suggestions
     * @param array $searchableFields An array of fields to search within for generating suggestions. Defaults to ['name'].
     * @return $this returns the instance of the current object to allow method chaining
     */
    public function autocomplete(string $keyword, array $searchableFields = ['name']): self
    {
        $this->queryContext->addMultiMatchSearch(
            keyword: $keyword,
            searchableFields: $searchableFields,
            type: 'bool_prefix'
        );
        return $this;
    }

    /**
     * Performs a search operation using the specified keyword and fields.
     * @param string $keyword the search keyword to be used in the query
     * @param array $fields An array of fields to search within. Defaults to ['name'].
     * @return $this returns the instance of the current object for method chaining
     */
    public function search(string $keyword, array $fields = ['name'], string $searchType = 'bool_prefix'): self
    {
        foreach ($fields as $field) {
            $fields[] = $field . '.search';
            $fields[] = $field . '.search._2gram';
            $fields[] = $field . '.search._3gram';
        }

        $this->queryContext->addMultiMatchSearch($keyword, $fields, $searchType);
        return $this;
    }

    public function addAggregation(string $name, array $params): self
    {
        $this->queryContext->addAggregation($name, $params);
        return $this;
    }

    /**
     * Parses an exception to extract a meaningful error message.
     * @param Throwable $exception the exception to parse
     * @return string the parsed error message
     */
    protected function parseError(Throwable $exception): string
    {
        $errorDetails = json_decode($exception->getMessage(), true);
        $message = __('hf-elastic.invalid_query') . ': ' . $exception->getMessage();

        if (json_last_error() === JSON_ERROR_NONE && isset($errorDetails['error']['reason'])) {
            $message = $errorDetails['error']['root_cause'][0]['reason'] ?? $errorDetails['error']['reason'];
        }

        return $message;
    }
}
