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
 * Strategy for handling equality operator (=).
 */
class EqualsOperator implements OperatorStrategyInterface
{
    public function apply(string $field, mixed $value, string $context): array
    {
        return ['term' => [$field => $value]];
    }

    public function supports(string $operator): bool
    {
        return $operator === '=';
    }
}
