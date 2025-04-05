<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

/**
 * Not exists operator.
 */
class NotExistsOperator extends AbstractOperator
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = 'not exists';
    }

    /**
     * {@inheritdoc}
     */
    public function apply(string $field, mixed $value): array
    {
        return [
            'bool' => [
                'must_not' => [
                    'exists' => [
                        'field' => $field,
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
        return true;
    }
}
