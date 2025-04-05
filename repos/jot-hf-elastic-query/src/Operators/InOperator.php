<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

/**
 * In operator.
 */
class InOperator extends AbstractOperator
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'in';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value): array
    {
        return [
            'terms' => [
                $field => $value,
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
