<?php

declare(strict_types=1);

namespace Jot\HfElastic\Factories;

use Jot\HfElastic\Contracts\QueryBuilderInterface;
use function Hyperf\Support\make;

/**
 * Factory for creating query builder instances.
 */
class QueryBuilderFactory
{
    /**
     * Creates a new query builder instance.
     * @return QueryBuilderInterface A new query builder instance.
     */
    public function create(): QueryBuilderInterface
    {
        return make(QueryBuilderInterface::class);
    }
}
