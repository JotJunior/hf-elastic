<?php

declare(strict_types=1);

namespace Jot\HfElastic\Query;

/**
 * Represents the context and state of an Elasticsearch query.
 */
class QueryContext
{
    /**
     * @var string|null The index to query.
     */
    private ?string $index = null;
    
    /**
     * @var array|null Additional indices to query.
     */
    private ?array $additionalIndices = null;
    
    /**
     * @var array The query body.
     */
    private array $body = [];
    
    /**
     * @var array The query conditions.
     */
    private array $query = [];
    
    /**
     * @var array The aggregations.
     */
    private array $aggs = [];
    
    /**
     * Sets the index for the query.
     * @param string $index The index name.
     * @return self
     */
    public function setIndex(string $index): self
    {
        $this->index = $index;
        return $this;
    }
    
    /**
     * Gets the current index.
     * @return string|null The current index or null if not set.
     */
    public function getIndex(): ?string
    {
        return $this->index;
    }
    
    /**
     * Sets additional indices for the query.
     * @param array|null $indices The additional indices.
     * @return self
     */
    public function setAdditionalIndices(?array $indices): self
    {
        $this->additionalIndices = $indices;
        return $this;
    }
    
    /**
     * Gets the additional indices.
     * @return array|null The additional indices or null if not set.
     */
    public function getAdditionalIndices(): ?array
    {
        return $this->additionalIndices;
    }
    
    /**
     * Adds a condition to the query.
     * @param array $condition The condition to add.
     * @param string $context The context to add the condition to (must, must_not, should).
     * @return self
     */
    public function addCondition(array $condition, string $context = 'must'): self
    {
        if (!isset($this->query['bool'][$context])) {
            $this->query['bool'][$context] = [];
        }
        
        $this->query['bool'][$context][] = $condition;
        return $this;
    }
    
    /**
     * Gets the current query.
     * @return array The current query.
     */
    public function getQuery(): array
    {
        return $this->query;
    }
    
    /**
     * Sets a body parameter.
     * @param string $key The parameter key.
     * @param mixed $value The parameter value.
     * @return self
     */
    public function setBodyParam(string $key, mixed $value): self
    {
        $this->body[$key] = $value;
        return $this;
    }
    
    /**
     * Gets the current body.
     * @return array The current body.
     */
    public function getBody(): array
    {
        return $this->body;
    }
    
    /**
     * Adds an aggregation to the query.
     * @param string $name The aggregation name.
     * @param array $aggregation The aggregation definition.
     * @return self
     */
    public function addAggregation(string $name, array $aggregation): self
    {
        $this->aggs[$name] = $aggregation;
        return $this;
    }
    
    /**
     * Gets the current aggregations.
     * @return array The current aggregations.
     */
    public function getAggregations(): array
    {
        return $this->aggs;
    }
    
    /**
     * Resets the query context to its initial state.
     * @return self
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
     * Completely resets the query context to its initial state, including the index.
     * @return self
     */
    public function resetAll(): self
    {
        $this->index = null;
        return $this->reset();
    }
    
    /**
     * Converts the query context to an array suitable for Elasticsearch.
     * @return array The array representation of the query.
     */
    public function toArray(): array
    {
        // Add default filter for non-deleted documents
        if (!isset($this->query['bool']['filter'])) {
            $this->query['bool']['filter'] = [];
        }
        $this->query['bool']['filter'][] = ['term' => ['deleted' => false]];
        
        $result = [
            'index' => $this->index,
            'body' => [
                ...$this->body,
                'query' => $this->query
            ]
        ];
        
        if (!empty($this->aggs)) {
            $result['body']['aggs'] = $this->aggs;
        }
        
        return $result;
    }
}
