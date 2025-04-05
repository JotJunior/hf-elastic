<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Contracts;

/**
 * Interface for Elasticsearch query builder.
 */
interface QueryBuilderInterface
{
    /**
     * Set the index to query.
     *
     * @param string $index
     * @return self
     */
    public function index(string $index): self;

    /**
     * Add a where clause to the query.
     *
     * @param string $field
     * @param mixed $operator
     * @param mixed $value
     * @return self
     */
    public function where(string $field, mixed $operator, mixed $value = null): self;

    /**
     * Add a where in clause to the query.
     *
     * @param string $field
     * @param array $values
     * @return self
     */
    public function whereIn(string $field, array $values): self;

    /**
     * Add a where not in clause to the query.
     *
     * @param string $field
     * @param array $values
     * @return self
     */
    public function whereNotIn(string $field, array $values): self;

    /**
     * Add a where between clause to the query.
     *
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return self
     */
    public function whereBetween(string $field, mixed $from, mixed $to): self;

    /**
     * Add a where not between clause to the query.
     *
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return self
     */
    public function whereNotBetween(string $field, mixed $from, mixed $to): self;

    /**
     * Add a where null clause to the query.
     *
     * @param string $field
     * @return self
     */
    public function whereNull(string $field): self;

    /**
     * Add a where not null clause to the query.
     *
     * @param string $field
     * @return self
     */
    public function whereNotNull(string $field): self;

    /**
     * Add a where exists clause to the query.
     *
     * @param string $field
     * @return self
     */
    public function whereExists(string $field): self;

    /**
     * Add a where not exists clause to the query.
     *
     * @param string $field
     * @return self
     */
    public function whereNotExists(string $field): self;

    /**
     * Add a full text search clause to the query.
     *
     * @param string|array $fields
     * @param string $query
     * @param array $options
     * @return self
     */
    public function search(string|array $fields, string $query, array $options = []): self;

    /**
     * Add an order by clause to the query.
     *
     * @param string $field
     * @param string $direction
     * @return self
     */
    public function orderBy(string $field, string $direction = 'asc'): self;

    /**
     * Set the number of results to return.
     *
     * @param int $limit
     * @return self
     */
    public function limit(int $limit): self;

    /**
     * Set the number of results to skip.
     *
     * @param int $offset
     * @return self
     */
    public function offset(int $offset): self;

    /**
     * Add an aggregation to the query.
     *
     * @param string $name
     * @param array $aggregation
     * @return self
     */
    public function aggregate(string $name, array $aggregation): self;

    /**
     * Execute the query and get the results.
     *
     * @return array
     */
    public function get(): array;

    /**
     * Execute the query and get the first result.
     *
     * @return array|null
     */
    public function first(): ?array;

    /**
     * Execute the query and get the count of results.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Execute the query and get paginated results.
     *
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function paginate(int $perPage = 15, int $page = 1): array;

    /**
     * Get the query as an array.
     *
     * @return array
     */
    public function toArray(): array;
}
