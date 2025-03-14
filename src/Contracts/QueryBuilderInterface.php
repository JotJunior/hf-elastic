<?php

declare(strict_types=1);

namespace Jot\HfElastic\Contracts;

/**
 * Interface for building Elasticsearch queries.
 */
interface QueryBuilderInterface
{
    /**
     * Specifies the index to be used for the operation.
     * @param string $index The name of the index.
     * @return self Returns the current instance for method chaining.
     */
    public function into(string $index): self;
    
    /**
     * Sets the index for the current operation.
     * @param string $index The name of the index to set.
     * @return self
     */
    public function from(string $index): self;
    
    /**
     * Sets additional indices for the current operation.
     * @param string|array $index The index or indices to join.
     * @return self
     */
    public function join(string|array $index): self;
    
    /**
     * Adds a condition to the query based on the specified field, operator, and value.
     * @param string $field The field to apply the condition to.
     * @param mixed $operator The operator to apply or a value directly.
     * @param mixed|null $value The value to compare against; optional if the operator is used as a value.
     * @param string $context The context of the condition, such as 'must', 'must_not', or 'should'.
     * @return self
     */
    public function where(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self;
    
    /**
     * Adds an additional condition to the query using a logical "AND" operator.
     * @param string $field The field to apply the condition on.
     * @param mixed $operator The operator to use for the condition.
     * @param mixed|null $value The value to compare the field against. Optional.
     * @param string $context The context in which the condition will be applied.
     * @return self
     */
    public function andWhere(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self;
    
    /**
     * Adds a condition to the query with a logical "OR" relationship.
     * @param string $field The name of the field to which the condition applies.
     * @param mixed $operator The operator or value for the condition.
     * @param mixed|null $value The value to be used with the operator.
     * @param string $subContext The sub-context within the query.
     * @return self
     */
    public function orWhere(string $field, mixed $operator, mixed $value = null, string $subContext = 'should'): self;
    
    /**
     * Adds a "must" condition to the query by applying the provided callback to a subquery instance.
     * @param callable $callback A callback function that defines the conditions for the "must" clause.
     * @return self
     */
    public function whereMust(callable $callback): self;
    
    /**
     * Adds a "should" condition to the query by applying the provided callback to a new subquery instance.
     * @param callable $callback A callback function that defines the criteria for the "should" condition.
     * @return self
     */
    public function whereShould(callable $callback): self;
    
    /**
     * Adds a nested query to the current query.
     * @param string $path The path to the nested object in the query.
     * @param callable $callback A callback that defines the nested query logic.
     * @return self
     */
    public function whereNested(string $path, callable $callback): self;
    
    /**
     * Sets the limit for the number of results to retrieve.
     * @param int $limit The maximum number of results to retrieve.
     * @return self
     */
    public function limit(int $limit): self;
    
    /**
     * Sets the starting point for a query or operation.
     * @param int $offset The starting offset for the operation.
     * @return self
     */
    public function offset(int $offset): self;
    
    /**
     * Specifies the sorting criteria for the query by field and order.
     * @param string $field The name of the field to sort by.
     * @param string $order The sorting order, either 'asc' for ascending or 'desc' for descending.
     * @return self
     */
    public function orderBy(string $field, string $order = 'asc'): self;
    
    /**
     * Adds a geo-distance query to filter results.
     * @param string $field The name of the field to apply the geo-distance filter.
     * @param string $location The geographic location specified as a coordinate.
     * @param string $distance The maximum distance from the location.
     * @return self
     */
    public function geoDistance(string $field, string $location, string $distance): self;
    
    /**
     * Specifies the fields to be retrieved from the data source.
     * @param string|array $fields The field or fields to select.
     * @return self
     */
    public function select(string|array $fields = '*'): self;
    
    /**
     * Counts the number of documents in the index matching the specified query.
     * @return int Returns the total number of documents that satisfy the query criteria.
     */
    public function count(): int;
    
    /**
     * Executes a search query on the specified index and retrieves matching results.
     * @return array Returns an array of search hits retrieved from the query execution.
     */
    public function execute(): array;
    
    /**
     * Converts the current query data into an array format suitable for execution.
     * @return array Returns an associative array representation of the query.
     */
    public function toArray(): array;
}
