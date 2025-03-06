<?php

declare(strict_types=1);

namespace Jot\HfElastic\Facade;

use Hyperf\Contract\ContainerInterface;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Factories\QueryBuilderFactory;

/**
 * Facade for the QueryBuilder to maintain backward compatibility.
 * 
 * This class provides a static interface to the QueryBuilder functionality
 * while delegating to the new implementation under the hood.
 */
class QueryBuilder
{
    /**
     * @var ContainerInterface The dependency injection container.
     */
    protected static ContainerInterface $container;
    
    /**
     * @var QueryBuilderInterface The query builder instance.
     */
    protected static QueryBuilderInterface $instance;
    
    /**
     * Set the container instance.
     *
     * @param ContainerInterface $container The dependency injection container.
     */
    public static function setContainer(ContainerInterface $container): void
    {
        static::$container = $container;
    }
    
    /**
     * Get or create the query builder instance.
     *
     * @return QueryBuilderInterface The query builder instance.
     */
    protected static function getInstance(): QueryBuilderInterface
    {
        if (!isset(static::$instance)) {
            $factory = static::$container->get(QueryBuilderFactory::class);
            static::$instance = $factory->create();
        }
        
        return static::$instance;
    }
    
    /**
     * Handle static method calls by delegating to the query builder instance.
     *
     * @param string $method The method name.
     * @param array $arguments The method arguments.
     * @return mixed The method result.
     */
    public static function __callStatic(string $method, array $arguments)
    {
        return static::getInstance()->$method(...$arguments);
    }
}
