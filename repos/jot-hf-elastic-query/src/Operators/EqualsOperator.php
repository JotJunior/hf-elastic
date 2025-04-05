<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

/**
 * Equals operator.
 */
class EqualsOperator extends AbstractOperator
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = '=';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value): array
    {
        return [
            'term' => [
                $field => $value,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $value): bool
    {
        return $value !== null;
    }
}
