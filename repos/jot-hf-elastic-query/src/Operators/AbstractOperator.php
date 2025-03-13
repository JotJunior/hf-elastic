<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

use Jot\HfElasticQuery\Contracts\OperatorInterface;

/**
 * Abstract base class for operators.
 */
abstract class AbstractOperator implements OperatorInterface
{
    /**
     * The operator name.
     *
     * @var string
     */
    protected string $name;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function apply(string $field, mixed $value): array;

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $value): bool
    {
        return true;
    }
}
