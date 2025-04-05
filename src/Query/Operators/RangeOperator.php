<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Query\Operators;

use InvalidArgumentException;
use Jot\HfElastic\Contracts\OperatorStrategyInterface;

/**
 * Strategy for handling range operators (>, <, >=, <=).
 */
class RangeOperator implements OperatorStrategyInterface
{
    /**
     * @var string the current operator being processed
     */
    private string $currentOperator;

    public function apply(string $field, mixed $value, string $context): array
    {
        if ($this->currentOperator === 'between' && is_array($value) && count($value) === 2) {
            return ['range' => [$field => [
                'gte' => $value[0],
                'lte' => $value[1],
            ]]];
        }

        $rangeType = $this->getRangeType($this->currentOperator);
        return ['range' => [$field => [$rangeType => $value]]];
    }

    public function supports(string $operator): bool
    {
        $this->currentOperator = $operator;
        return in_array($operator, ['>', '<', '>=', '<=', 'between']);
    }

    /**
     * Maps the operator to the corresponding Elasticsearch range type.
     * @param string $operator the operator to map
     * @return string the corresponding Elasticsearch range type
     */
    private function getRangeType(string $operator): string
    {
        return match ($operator) {
            '>' => 'gt',
            '<' => 'lt',
            '>=' => 'gte',
            '<=' => 'lte',
            default => throw new InvalidArgumentException("Unsupported range operator: {$operator}")
        };
    }
}
