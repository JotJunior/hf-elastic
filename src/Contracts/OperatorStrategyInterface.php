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
 * Interface for operator strategies used in query building.
 */
interface OperatorStrategyInterface
{
    /**
     * Applies the operator strategy to build a query clause.
     * @param string $field the field to apply the condition to
     * @param mixed $value the value to use in the condition
     * @param string $context the context of the condition (must, must_not, should)
     * @return array the query clause array
     */
    public function apply(string $field, mixed $value, string $context): array;

    /**
     * Checks if this strategy supports the given operator.
     * @param string $operator the operator to check
     * @return bool true if this strategy supports the operator, false otherwise
     */
    public function supports(string $operator): bool;
}
