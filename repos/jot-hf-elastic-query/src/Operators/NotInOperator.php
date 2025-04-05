<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

/**
 * Not in operator.
 */
class NotInOperator extends AbstractOperator
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'not in';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value): array
    {
        return [
            'bool' => [
                'must_not' => [
                    'terms' => [
                        $field => $value,
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
        return is_array($value) && !empty($value);
    }
}
