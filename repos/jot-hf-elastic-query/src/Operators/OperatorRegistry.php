<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Operators;

use InvalidArgumentException;
use Jot\HfElasticQuery\Contracts\OperatorInterface;
use Jot\HfElasticQuery\Contracts\OperatorRegistryInterface;

/**
 * Registry for query operators.
 */
class OperatorRegistry implements OperatorRegistryInterface
{
    /**
     * The registered operators.
     *
     * @var array<string, string>
     */
    protected array $operators = [];

    /**
     * The operator instances.
     *
     * @var array<string, OperatorInterface>
     */
    protected array $instances = [];

    /**
     * Constructor.
     *
     * @param array<string, string> $operators
     */
    public function __construct(array $operators = [])
    {
        foreach ($operators as $name => $operatorClass) {
            $this->register($name, $operatorClass);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register(string $name, string $operatorClass): self
    {
        if (!class_exists($operatorClass)) {
            throw new InvalidArgumentException(sprintf(
                'Operator class "%s" does not exist.',
                $operatorClass
            ));
        }

        if (!is_subclass_of($operatorClass, OperatorInterface::class)) {
            throw new InvalidArgumentException(sprintf(
                'Operator class "%s" must implement %s.',
                $operatorClass,
                OperatorInterface::class
            ));
        }

        $this->operators[$name] = $operatorClass;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): OperatorInterface
    {
        if (!$this->has($name)) {
            throw new InvalidArgumentException(sprintf(
                'Operator "%s" is not registered.',
                $name
            ));
        }

        if (!isset($this->instances[$name])) {
            $operatorClass = $this->operators[$name];
            $this->instances[$name] = new $operatorClass();
        }

        return $this->instances[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return isset($this->operators[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->operators;
    }
}
