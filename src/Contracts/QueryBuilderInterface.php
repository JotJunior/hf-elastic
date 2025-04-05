<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Contracts;

/**
 * Interface for building Elasticsearch queries.
 */
interface QueryBuilderInterface
{
    /**
     * Specifies the fields to be retrieved from the data source.
     * @param array|string $fields the field or fields to select
     */
    public function select(array|string $fields = '*'): self;

    /**
     * Sets the index for the current operation.
     * @param string $index the name of the index to set
     */
    public function from(string $index): self;

    /**
     * Specifies the index to be used for the operation.
     * @param string $index the name of the index
     * @return self returns the current instance for method chaining
     */
    public function into(string $index): self;

    /**
     * Sets additional indices for the current operation.
     * @param array|string $index the index or indices to join
     */
    public function join(array|string $index): self;

    /**
     * Adds a condition to the query based on the specified field, operator, and value.
     * @param string $field the field to apply the condition to
     * @param mixed $operator the operator to apply or a value directly
     * @param null|mixed $value the value to compare against; optional if the operator is used as a value
     * @param string $context the context of the condition, such as 'must', 'must_not', or 'should'
     */
    public function where(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self;

    /**
     * Adds an additional condition to the query using a logical "AND" operator.
     * @param string $field the field to apply the condition on
     * @param mixed $operator the operator to use for the condition
     * @param null|mixed $value The value to compare the field against. Optional.
     * @param string $context the context in which the condition will be applied
     */
    public function andWhere(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self;

    /**
     * Adds a condition to the query with a logical "OR" relationship.
     * @param string $field the name of the field to which the condition applies
     * @param mixed $operator the operator or value for the condition
     * @param null|mixed $value the value to be used with the operator
     * @param string $subContext the sub-context within the query
     */
    public function orWhere(string $field, mixed $operator, mixed $value = null, string $subContext = 'should'): self;

    /**
     * Adds a "must" condition to the query by applying the provided callback to a subquery instance.
     * @param callable $callback a callback function that defines the conditions for the "must" clause
     */
    public function whereMust(callable $callback): self;

    /**
     * Adds a "should" condition to the query by applying the provided callback to a new subquery instance.
     * @param callable $callback a callback function that defines the criteria for the "should" condition
     */
    public function whereShould(callable $callback): self;

    /**
     * Adds a nested query to the current query.
     * @param string $path the path to the nested object in the query
     * @param callable $callback a callback that defines the nested query logic
     */
    public function whereNested(string $path, callable $callback): self;

    /**
     * Sets the limit for the number of results to retrieve.
     * @param int $limit the maximum number of results to retrieve
     */
    public function limit(int $limit): self;

    /**
     * Sets the starting point for a query or operation.
     * @param int $offset the starting offset for the operation
     */
    public function offset(int $offset): self;

    /**
     * Specifies the sorting criteria for the query by field and order.
     * @param string $field the name of the field to sort by
     * @param string $order the sorting order, either 'asc' for ascending or 'desc' for descending
     */
    public function orderBy(string $field, string $order = 'asc'): self;

    /**
     * Adds a geo-distance query to filter results.
     * @param string $field the name of the field to apply the geo-distance filter
     * @param string $location the geographic location specified as a coordinate
     * @param string $distance the maximum distance from the location
     */
    public function geoDistance(string $field, string $location, string $distance): self;

    /**
     * Appends a specified suffix to the current index name.
     * @param string $suffix the suffix to append
     */
    public function withSuffix(string $suffix): self;

    /**
     * Converts the current query data into an array format suitable for execution.
     * @return array returns an associative array representation of the query
     */
    public function toArray(): array;
}
