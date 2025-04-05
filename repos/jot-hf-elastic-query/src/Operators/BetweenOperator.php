<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

/**
 * Between operator.
 */
class BetweenOperator extends AbstractOperator
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'between';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value): array
    {
        if (!is_array($value) || count($value) !== 2) {
            throw new \InvalidArgumentException('Between operator requires an array with exactly two elements.');
        }

        return [
            'range' => [
                $field => [
                    'gte' => $value[0],
                    'lte' => $value[1],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $value): bool
    {
        return is_array($value) && count($value) === 2;
    }
}
