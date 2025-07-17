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

class QueryContext
{
    private ?string $index = null;

    private ?array $additionalIndices = null;

    private array $body = [];

    private array $query = [];

    private array $aggs = [];

    public function __destruct()
    {
        $this->resetAll();
    }

    /**
     * Completely resets the query context to its initial state, including the index.
     */
    public function resetAll(): self
    {
        $this->index = null;
        return $this->reset();
    }

    /**
     * Resets the query context to its initial state.
     */
    public function reset(): self
    {
        $this->additionalIndices = null;
        $this->query = [];
        $this->body = [];
        $this->aggs = [];
        return $this;
    }

    /**
     * Gets the current index.
     * @return null|string the current index or null if not set
     */
    public function getIndex(): ?string
    {
        return $this->index;
    }

    /**
     * Sets the index for the query.
     * @param string $index the index name
     */
    public function setIndex(string $index): self
    {
        $this->index = $index;
        return $this;
    }

    /**
     * Gets the additional indices.
     * @return null|array the additional indices or null if not set
     */
    public function getAdditionalIndices(): ?array
    {
        return $this->additionalIndices;
    }

    /**
     * Sets additional indices for the query.
     * @param null|array $indices the additional indices
     */
    public function setAdditionalIndices(?array $indices): self
    {
        $this->additionalIndices = $indices;
        return $this;
    }

    /**
     * Adds a multi-match search condition to the query.
     * @param string $keyword the search keyword
     * @param array $searchableFields the fields to search within
     * @param string $type the type of multi-match query, defaults to 'cross_fields'
     * @param string $context the context in which the condition is applied, defaults to 'must'
     */
    public function addMultiMatchSearch(string $keyword, array $searchableFields, string $type = 'cross_fields', string $context = 'must'): self
    {
        $condition = [
            'multi_match' => [
                'query' => $keyword,
                'type' => $type,
                'fields' => $searchableFields,
                'operator' => 'or',
            ],
        ];
        $this->addCondition($condition, $context);
        return $this;
    }

    /**
     * Adds a condition to the query.
     * @param array $condition the condition to add
     * @param string $context the context to add the condition to (must, must_not, should)
     */
    public function addCondition(array $condition, string $context = 'must'): self
    {
        if (! isset($this->query['bool'][$context])) {
            $this->query['bool'][$context] = [];
        }

        $this->query['bool'][$context][] = $condition;
        return $this;
    }

    /**
     * Gets the current query.
     * @return array the current query
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Sets a body parameter.
     * @param string $key the parameter key
     * @param mixed $value the parameter value
     */
    public function setBodyParam(string $key, mixed $value): self
    {
        $this->body[$key] = $value;
        return $this;
    }

    /**
     * Gets the current body.
     * @return array the current body
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Adds an aggregation to the query.
     * @param string $name the aggregation name
     * @param array $aggregation the aggregation definition
     */
    public function addAggregation(string $name, array $aggregation): self
    {
        $this->aggs[$name] = $aggregation;
        return $this;
    }

    /**
     * Gets the current aggregations.
     * @return array the current aggregations
     */
    public function getAggregations(): array
    {
        return $this->aggs;
    }

    /**
     * Converts the query context to an array suitable for Elasticsearch.
     * @return array the array representation of the query
     */
    public function toArray(): array
    {
        // Add default filter for non-deleted documents
        if (! isset($this->query['bool']['filter'])) {
            $this->query['bool']['filter'] = [];
        }
        $this->query['bool']['filter'][] = ['term' => ['deleted' => false]];

        $this->query['bool']['filter'] = array_values(array_unique($this->query['bool']['filter'], SORT_REGULAR));

        $result = [
            'index' => $this->index,
            'body' => [
                ...$this->body,
                'query' => $this->query,
            ],
        ];

        if (! empty($this->aggs)) {
            $result['body']['aggs'] = $this->aggs;
        }

        return $result;
    }
}
