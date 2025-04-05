<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Contracts;

/**
 * Interface for query operators.
 */
interface OperatorInterface
{
    /**
     * Get the operator name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Apply the operator to the query.
     *
     * @param string $field
     * @param mixed $value
     * @return array
     */
    public function apply(string $field, mixed $value): array;

    /**
     * Check if the operator supports the given value.
     *
     * @param mixed $value
     * @return bool
     */
    public function supports(mixed $value): bool;
}
