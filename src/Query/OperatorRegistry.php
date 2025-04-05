<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Query;

use Jot\HfElastic\Contracts\OperatorStrategyInterface;

/**
 * Registry for operator strategies used in query building.
 */
class OperatorRegistry
{
    /**
     * @var OperatorStrategyInterface[] array of registered operator strategies
     */
    private array $strategies = [];

    /**
     * Registers a new operator strategy.
     * @param OperatorStrategyInterface $strategy the strategy to register
     */
    public function register(OperatorStrategyInterface $strategy): self
    {
        $this->strategies[] = $strategy;
        return $this;
    }

    /**
     * Finds a strategy that supports the given operator.
     * @param string $operator the operator to find a strategy for
     * @return null|OperatorStrategyInterface the matching strategy or null if none found
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
