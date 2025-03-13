<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Context;

use Jot\HfElasticQuery\Contracts\QueryContextInterface;

/**
 * Query context implementation.
 */
class QueryContext implements QueryContextInterface
{
    /**
     * The index name.
     *
     * @var string|null
     */
    protected ?string $index = null;

    /**
     * The query body.
     *
     * @var array
     */
    protected array $query = [];

    /**
     * The filter clauses.
     *
     * @var array
     */
    protected array $filters = [];

    /**
     * The must clauses.
     *
     * @var array
     */
    protected array $must = [];

    /**
     * The must not clauses.
     *
     * @var array
     */
    protected array $mustNot = [];

    /**
     * The should clauses.
     *
     * @var array
     */
    protected array $should = [];

    /**
     * The minimum should match.
     *
     * @var int|string|null
     */
    protected int|string|null $minimumShouldMatch = null;

    /**
     * The sort clauses.
     *
     * @var array
     */
    protected array $sort = [];

    /**
     * The from parameter.
     *
     * @var int
     */
    protected int $from = 0;

    /**
     * The size parameter.
     *
     * @var int
     */
    protected int $size = 10;

    /**
     * The aggregations.
     *
     * @var array
     */
    protected array $aggregations = [];

    /**
     * {@inheritdoc}
     */
    public function getIndex(): ?string
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndex(string $index): self
    {
        $this->index = $index;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuery(array $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(array $filter): self
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function addMust(array $must): self
    {
        $this->must[] = $must;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMust(): array
    {
        return $this->must;
    }

    /**
     * {@inheritdoc}
     */
    public function addMustNot(array $mustNot): self
    {
        $this->mustNot[] = $mustNot;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMustNot(): array
    {
        return $this->mustNot;
    }

    /**
     * {@inheritdoc}
     */
    public function addShould(array $should): self
    {
        $this->should[] = $should;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShould(): array
    {
        return $this->should;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinimumShouldMatch(int|string $minimumShouldMatch): self
    {
        $this->minimumShouldMatch = $minimumShouldMatch;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimumShouldMatch(): int|string|null
    {
        return $this->minimumShouldMatch;
    }

    /**
     * {@inheritdoc}
     */
    public function addSort(string $field, string $direction = 'asc'): self
    {
        $this->sort[] = [$field => ['order' => $direction]];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSort(): array
    {
        return $this->sort;
    }

    /**
     * {@inheritdoc}
     */
    public function setFrom(int $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrom(): int
    {
        return $this->from;
    }

    /**
     * {@inheritdoc}
     */
    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function addAggregation(string $name, array $aggregation): self
    {
        $this->aggregations[$name] = $aggregation;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregations(): array
    {
        return $this->aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): array
    {
        $params = [];

        if ($this->index !== null) {
            $params['index'] = $this->index;
        }

        $body = [];

        if (!empty($this->query)) {
            $body['query'] = $this->query;
        } else {
            $boolQuery = [];

            if (!empty($this->must)) {
                $boolQuery['must'] = $this->must;
            }

            if (!empty($this->mustNot)) {
                $boolQuery['must_not'] = $this->mustNot;
            }

            if (!empty($this->should)) {
                $boolQuery['should'] = $this->should;

                if ($this->minimumShouldMatch !== null) {
                    $boolQuery['minimum_should_match'] = $this->minimumShouldMatch;
                }
            }

            if (!empty($this->filters)) {
                $boolQuery['filter'] = $this->filters;
            }

            if (!empty($boolQuery)) {
                $body['query'] = ['bool' => $boolQuery];
            }
        }

        if (!empty($this->sort)) {
            $body['sort'] = $this->sort;
        }

        if ($this->from > 0) {
            $body['from'] = $this->from;
        }

        if ($this->size !== 10) {
            $body['size'] = $this->size;
        }

        if (!empty($this->aggregations)) {
            $body['aggs'] = $this->aggregations;
        }

        if (!empty($body)) {
            $params['body'] = $body;
        }

        return $params;
    }
}
