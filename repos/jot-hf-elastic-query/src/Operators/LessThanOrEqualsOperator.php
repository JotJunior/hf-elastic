<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

/**
 * Less than or equals operator.
 */
class LessThanOrEqualsOperator extends AbstractOperator
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = '<=';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value): array
    {
        return [
            'range' => [
                $field => [
                    'lte' => $value,
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
