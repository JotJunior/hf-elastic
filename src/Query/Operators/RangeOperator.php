<?php

declare(strict_types=1);

namespace Jot\HfElastic\Query\Operators;

use Jot\HfElastic\Contracts\OperatorStrategyInterface;

/**
 * Strategy for handling range operators (>, <, >=, <=).
 */
class RangeOperator implements OperatorStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value, string $context): array
    {
        if ($this->currentOperator === 'between' && is_array($value) && count($value) === 2) {
            return ['range' => [$field => [
                'gte' => $value[0],
                'lte' => $value[1]
            ]]];
        }
        
        $rangeType = $this->getRangeType($this->currentOperator);
        return ['range' => [$field => [$rangeType => $value]]];
    }
    
    /**
     * {@inheritdoc}
     */
    public function supports(string $operator): bool
    {
        $this->currentOperator = $operator;
        return in_array($operator, ['>', '<', '>=', '<=', 'between']);
    }
    
    /**
     * Maps the operator to the corresponding Elasticsearch range type.
     *
     * @param string $operator The operator to map.
     * @return string The corresponding Elasticsearch range type.
     */
    private function getRangeType(string $operator): string
    {
        return match($operator) {
            '>' => 'gt',
            '<' => 'lt',
            '>=' => 'gte',
            '<=' => 'lte',
            default => throw new \InvalidArgumentException("Unsupported range operator: {$operator}")
        };
    }
    
    /**
     * @var string The current operator being processed.
     */
    private string $currentOperator;
}
