<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

/**
 * Greater than operator.
 */
class GreaterThanOperator extends AbstractOperator
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = '>';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value): array
    {
        return [
            'range' => [
                $field => [
                    'gt' => $value,
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $value): bool
    {
        return is_numeric($value) || is_string($value);
    }
}
