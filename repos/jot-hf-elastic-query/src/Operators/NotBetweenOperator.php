<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

/**
 * Not between operator.
 */
class NotBetweenOperator extends AbstractOperator
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'not between';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value): array
    {
        if (!is_array($value) || count($value) !== 2) {
            throw new \InvalidArgumentException('Not between operator requires an array with exactly two elements.');
        }

        return [
            'bool' => [
                'must_not' => [
                    'range' => [
                        $field => [
                            'gte' => $value[0],
                            'lte' => $value[1],
                        ],
                    ],
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
