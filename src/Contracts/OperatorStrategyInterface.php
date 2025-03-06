<?php

declare(strict_types=1);

namespace Jot\HfElastic\Contracts;

/**
 * Interface for operator strategies used in query building.
 */
interface OperatorStrategyInterface
{
    /**
     * Applies the operator strategy to build a query clause.
     *
     * @param string $field The field to apply the condition to.
     * @param mixed $value The value to use in the condition.
     * @param string $context The context of the condition (must, must_not, should).
     * @return array The query clause array.
     */
    public function apply(string $field, mixed $value, string $context): array;
    
    /**
     * Checks if this strategy supports the given operator.
     *
     * @param string $operator The operator to check.
     * @return bool True if this strategy supports the operator, false otherwise.
     */
    public function supports(string $operator): bool;
}
