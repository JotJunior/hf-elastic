<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Facade;

use Hyperf\Contract\ContainerInterface;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Factories\QueryBuilderFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Facade for the QueryBuilder to maintain backward compatibility.
 * This class provides a static interface to the QueryBuilder functionality
 * while delegating to the new implementation under the hood.
 */
class QueryBuilder
{
    /**
     * @var ContainerInterface the dependency injection container
     */
    protected static ContainerInterface $container;

    /**
     * @var QueryBuilderInterface the query builder instance
     */
    protected static QueryBuilderInterface $instance;

    /**
     * Handle static method calls by delegating to the query builder instance.
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function __callStatic(string $method, array $arguments)
    {
        return static::getInstance()->{$method}(...$arguments);
    }

    /**
     * Set the container instance.
     * @param ContainerInterface $container the dependency injection container
     */
    public static function setContainer(ContainerInterface $container): void
    {
        static::$container = $container;
    }

    /**
     * Get or create the query builder instance.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function getInstance(): QueryBuilderInterface
    {
        if (! isset(static::$instance)) {
            $factory = static::$container->get(QueryBuilderFactory::class);
            static::$instance = $factory->create();
        }

        return static::$instance;
    }
}
