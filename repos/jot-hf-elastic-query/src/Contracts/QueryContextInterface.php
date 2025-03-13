<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Contracts;

/**
 * Interface for query context.
 */
interface QueryContextInterface
{
    /**
     * Get the index name.
     *
     * @return string|null
     */
    public function getIndex(): ?string;

    /**
     * Set the index name.
     *
     * @param string $index
     * @return self
     */
    public function setIndex(string $index): self;

    /**
     * Get the query body.
     *
     * @return array
     */
    public function getQuery(): array;

    /**
     * Set the query body.
     *
     * @param array $query
     * @return self
     */
    public function setQuery(array $query): self;

    /**
     * Add a filter to the query.
     *
     * @param array $filter
     * @return self
     */
    public function addFilter(array $filter): self;

    /**
     * Get all filters.
     *
     * @return array
     */
    public function getFilters(): array;

    /**
     * Add a must clause to the query.
     *
     * @param array $must
     * @return self
     */
    public function addMust(array $must): self;

    /**
     * Get all must clauses.
     *
     * @return array
     */
    public function getMust(): array;

    /**
     * Add a must not clause to the query.
     *
     * @param array $mustNot
     * @return self
     */
    public function addMustNot(array $mustNot): self;

    /**
     * Get all must not clauses.
     *
     * @return array
     */
    public function getMustNot(): array;

    /**
     * Add a should clause to the query.
     *
     * @param array $should
     * @return self
     */
    public function addShould(array $should): self;

    /**
     * Get all should clauses.
     *
     * @return array
     */
    public function getShould(): array;

    /**
     * Set the minimum should match.
     *
     * @param int|string $minimumShouldMatch
     * @return self
     */
    public function setMinimumShouldMatch(int|string $minimumShouldMatch): self;

    /**
     * Get the minimum should match.
     *
     * @return int|string|null
     */
    public function getMinimumShouldMatch(): int|string|null;

    /**
     * Add a sort to the query.
     *
     * @param string $field
     * @param string $direction
     * @return self
     */
    public function addSort(string $field, string $direction = 'asc'): self;

    /**
     * Get all sorts.
     *
     * @return array
     */
    public function getSort(): array;

    /**
     * Set the from parameter.
     *
     * @param int $from
     * @return self
     */
    public function setFrom(int $from): self;

    /**
     * Get the from parameter.
     *
     * @return int
     */
    public function getFrom(): int;

    /**
     * Set the size parameter.
     *
     * @param int $size
     * @return self
     */
    public function setSize(int $size): self;

    /**
     * Get the size parameter.
     *
     * @return int
     */
    public function getSize(): int;

    /**
     * Add an aggregation to the query.
     *
     * @param string $name
     * @param array $aggregation
     * @return self
     */
    public function addAggregation(string $name, array $aggregation): self;

    /**
     * Get all aggregations.
     *
     * @return array
     */
    public function getAggregations(): array;

    /**
     * Build the final query array.
     *
     * @return array
     */
    public function build(): array;
}
