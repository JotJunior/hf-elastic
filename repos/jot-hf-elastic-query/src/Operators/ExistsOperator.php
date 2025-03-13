<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

/**
 * Exists operator.
 */
class ExistsOperator extends AbstractOperator
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'exists';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value): array
    {
        return [
            'exists' => [
                'field' => $field,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $value): bool
    {
        return true;
    }
}
