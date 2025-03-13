<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Contracts;

/**
 * Interface for operator registry.
 */
interface OperatorRegistryInterface
{
    /**
     * Register an operator.
     *
     * @param string $name
     * @param string $operatorClass
     * @return self
     */
    public function register(string $name, string $operatorClass): self;

    /**
     * Get an operator by name.
     *
     * @param string $name
     * @return OperatorInterface
     * @throws \InvalidArgumentException
     */
    public function get(string $name): OperatorInterface;

    /**
     * Check if an operator is registered.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Get all registered operators.
     *
     * @return array<string, string>
     */
    public function all(): array;
}
