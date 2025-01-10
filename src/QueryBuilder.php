<?php

/**
 * Class QueryBuilder
 * Provides a fluent interface to build complex Elasticsearch queries.
 */

namespace Jot\HfElastic;

class QueryBuilder
{

    protected string $index;
    protected array $body = [];
    protected array $query = [];
    protected array $aggs = [];

    public function select(array $fields): self
    {
        $this->body['_source'] = $fields;
        return $this;
    }

    public function from(string $index): self
    {
        $this->index = $index;
        return $this;
    }

    public function where(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self
    {
        // Ensure the given context (must, must_not, should) exists within the bool query
        if (!isset($this->query['bool'][$context])) {
            $this->query['bool'][$context] = [];
        }

        // Handle cases where the operator is directly a value (e.g., `where('field', 'value')`)
        if (is_null($value)) {
            $value = $operator;
            $this->query['bool'][$context][] = ['term' => [$field => $value]];
            return $this;
        }

        // Handle cases with specific operators like '=', '!=', '>', '<', '>=', '<=', etc.
        switch ($operator) {
            case '=':
                $this->query['bool'][$context][] = ['term' => [$field => $value]];
                break;

            case '!=':
                if ($context === 'must') {
                    $this->query['bool']['must_not'][] = ['term' => [$field => $value]];
                } else {
                    $this->query['bool'][$context][] = ['bool' => ['must_not' => [['term' => [$field => $value]]]]];
                }
                break;

            case '>':
                $this->query['bool'][$context][] = ['range' => [$field => ['gt' => $value]]];
                break;

            case '<':
                $this->query['bool'][$context][] = ['range' => [$field => ['lt' => $value]]];
                break;

            case '>=':
                $this->query['bool'][$context][] = ['range' => [$field => ['gte' => $value]]];
                break;

            case '<=':
                $this->query['bool'][$context][] = ['range' => [$field => ['lte' => $value]]];
                break;

            case 'exists':
                $this->query['bool'][$context][] = ['exists' => ['field' => $value]];
                break;

            case 'distance':
                $this->query['bool'][$context][] = ['geo_distance' => ['distance' => $value['distance'], $field => ['lat' => $value['lat'], 'lon' => $value['lon']]]];
                break;

            case 'between':
                $this->query['bool'][$context][] = ['range' => [$field => ['gte' => $value[0], 'lte' => $value[1]]]];
                break;

            case 'like':
                $this->query['bool'][$context][] = ['wildcard' => [$field => str_replace('%', '*', $value)]];
                break;

            default:
                throw new \InvalidArgumentException("Unsupported operator: {$operator}");
        }

        return $this;
    }

    public function orWhere(string $field, mixed $operator, mixed $value = null, string $subContext = 'should'): self
    {
        // Ensure the top-level bool query and the should context exist
        if (!isset($this->query['bool'][$subContext])) {
            $this->query['bool'][$subContext] = [];
        }

        // Handle cases where the operator is directly a value (e.g., `orWhere('field', 'value')`)
        if (is_null($value)) {
            $value = $operator;
            $this->query['bool'][$subContext][] = ['term' => [$field => $value]];
            return $this;
        }

        // Handle cases with specific operators like '=', '!=', '>', '<', '>=', '<=', etc.
        switch ($operator) {
            case '=':
                $this->query['bool'][$subContext][] = ['term' => [$field => $value]];
                break;

            case '!=':
                $this->query['bool'][$subContext][] = ['bool' => ['must_not' => [['term' => [$field => $value]]]]];
                break;

            case '>':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['gt' => $value]]];
                break;

            case '<':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['lt' => $value]]];
                break;

            case '>=':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['gte' => $value]]];
                break;

            case '<=':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['lte' => $value]]];
                break;

            case 'exists':
                $this->query['bool'][$subContext][] = ['exists' => ['field' => $value]];
                break;

            case 'distance':
                $this->query['bool'][$subContext][] = ['geo_distance' => ['distance' => $value['distance'], $field => ['lat' => $value['lat'], 'lon' => $value['lon']]]];
                break;

            case 'between':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['gte' => $value[0], 'lte' => $value[1]]]];
                break;

            case 'like':
                $this->query['bool'][$subContext][] = ['wildcard' => [$field => str_replace('%', '*', $value)]];
                break;

            default:
                throw new \InvalidArgumentException("Unsupported operator: {$operator}");
        }

        return $this;
    }

    public function whereMust(callable $callback): self
    {
        $subQuery = new self();
        $callback($subQuery);
        $this->query['bool']['must'][] = ['bool' => $subQuery->query['bool']];
        return $this;
    }

    public function whereShould(callable $callback): self
    {
        $subQuery = new self();
        $callback($subQuery);
        $this->query['bool']['must'][] = ['bool' => ['should' => $subQuery->query['bool']['must']]];
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->body['size'] = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->body['from'] = $offset;
        return $this;
    }

    public function whereNested(string $path, callable $callback): self
    {
        $subQuery = new self();
        $callback($subQuery);
        $this->query['bool']['must'][] = ['nested' => ['path' => $path, 'query' => $subQuery->query]];
        return $this;
    }

    public function orderBy(string $field, string $order = 'asc'): self
    {
        $this->body['sort'][] = [$field => $order];
        return $this;
    }

    public function geoDistance(string $field, string $location, string $distance): self
    {
        $this->query['bool']['must'][] = ['geo_distance' => ['distance' => $distance, 'location' => $location]];
        return $this;
    }

    public function toArray(): array
    {
        $query = [
            'index' => $this->index,
            'body' => [
                ...$this->body,
                'query' => $this->query
            ]
        ];

        if (!empty($this->aggs)) {
            $query['body']['aggs'] = $this->aggs;
        }

        return $query;
    }

}