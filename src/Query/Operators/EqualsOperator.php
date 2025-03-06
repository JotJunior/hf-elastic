<?php

declare(strict_types=1);

namespace Jot\HfElastic\Query\Operators;

use Jot\HfElastic\Contracts\OperatorStrategyInterface;

/**
 * Strategy for handling equality operator (=).
 */
class EqualsOperator implements OperatorStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value, string $context): array
    {
        return ['term' => [$field => $value]];
    }
    
    /**
     * {@inheritdoc}
     */
    public function supports(string $operator): bool
    {
        return $operator === '=';
    }
}
