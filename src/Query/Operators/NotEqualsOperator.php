<?php

declare(strict_types=1);

namespace Jot\HfElastic\Query\Operators;

use Jot\HfElastic\Contracts\OperatorStrategyInterface;

/**
 * Strategy for handling inequality operator (!=).
 */
class NotEqualsOperator implements OperatorStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value, string $context): array
    {
        if ($context === 'must') {
            return ['bool' => ['must_not' => [['term' => [$field => $value]]]]];
        }
        
        return ['bool' => ['must_not' => [['term' => [$field => $value]]]]];
    }
    
    /**
     * {@inheritdoc}
     */
    public function supports(string $operator): bool
    {
        return $operator === '!=';
    }
}
