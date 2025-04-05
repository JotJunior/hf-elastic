<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

/**
 * Like operator.
 */
class LikeOperator extends AbstractOperator
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'like';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value): array
    {
        return [
            'wildcard' => [
                $field => [
                    'value' => $value,
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
