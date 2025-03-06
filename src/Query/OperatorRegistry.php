<?php

declare(strict_types=1);

namespace Jot\HfElastic\Query;

use Jot\HfElastic\Contracts\OperatorStrategyInterface;

/**
 * Registry for operator strategies used in query building.
 */
class OperatorRegistry
{
    /**
     * @var OperatorStrategyInterface[] Array of registered operator strategies.
     */
    private array $strategies = [];
    
    /**
     * Registers a new operator strategy.
     *
     * @param OperatorStrategyInterface $strategy The strategy to register.
     * @return self
     */
    public function register(OperatorStrategyInterface $strategy): self
    {
        $this->strategies[] = $strategy;
        return $this;
    }
    
    /**
     * Finds a strategy that supports the given operator.
     *
     * @param string $operator The operator to find a strategy for.
     * @return OperatorStrategyInterface|null The matching strategy or null if none found.
     */
    public function findStrategy(string $operator): ?OperatorStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($operator)) {
                return $strategy;
            }
        }
        
        return null;
    }
}
