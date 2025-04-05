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

use Jot\HfElastic\Contracts\OperatorStrategyInterface;

/**
 * Strategy for handling inequality operator (!=).
 */
class NotEqualsOperator implements OperatorStrategyInterface
{
    public function apply(string $field, mixed $value, string $context): array
    {
        if ($context === 'must') {
            return ['bool' => ['must_not' => [['term' => [$field => $value]]]]];
        }

        return ['bool' => ['must_not' => [['term' => [$field => $value]]]]];
    }

    public function supports(string $operator): bool
    {
        return $operator === '!=';
    }
}
