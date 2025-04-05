<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

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
     * @return QueryBuilderInterface a new query builder instance
     */
    public function create(): QueryBuilderInterface
    {
        return make(QueryBuilderInterface::class);
    }
}
