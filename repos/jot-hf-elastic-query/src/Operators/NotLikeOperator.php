<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

/**
 * Not like operator.
 */
class NotLikeOperator extends AbstractOperator
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'not like';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value): array
    {
        return [
            'bool' => [
                'must_not' => [
                    'wildcard' => [
                        $field => [
                            'value' => $value,
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
        return is_string($value) && !empty($value);
    }
}
