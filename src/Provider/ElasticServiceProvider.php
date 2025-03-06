<?php

declare(strict_types=1);

namespace Jot\HfElastic\Provider;

use Hyperf\Contract\ContainerInterface;
use Jot\HfElastic\ClientBuilder;
use Jot\HfElastic\Contracts\ClientFactoryInterface;
use Jot\HfElastic\Contracts\ElasticRepositoryInterface;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Factories\QueryBuilderFactory;
use Jot\HfElastic\Query\ElasticQueryBuilder;
use Jot\HfElastic\Query\OperatorRegistry;
use Jot\HfElastic\Query\Operators\EqualsOperator;
use Jot\HfElastic\Query\Operators\NotEqualsOperator;
use Jot\HfElastic\Query\Operators\RangeOperator;
use Jot\HfElastic\Query\QueryContext;
use Jot\HfElastic\Repository\ElasticRepository;
use Jot\HfElastic\Services\IndexNameFormatter;

/**
 * Service provider for registering Elasticsearch-related services.
 */
class ElasticServiceProvider
{
    /**
     * Register services into the container.
     *
     * @param ContainerInterface $container The dependency injection container.
     */
    public function register(ContainerInterface $container): void
    {
        // Register interfaces to implementations
        $container->define(ClientFactoryInterface::class, ClientBuilder::class);
        $container->define(QueryBuilderInterface::class, ElasticQueryBuilder::class);
        $container->define(ElasticRepositoryInterface::class, ElasticRepository::class);
        
        // Register operator registry with default operators
        $container->define(OperatorRegistry::class, function () {
            $registry = new OperatorRegistry();
            $registry->register(new EqualsOperator());
            $registry->register(new NotEqualsOperator());
            $registry->register(new RangeOperator());
            // Register additional operators as needed
            return $registry;
        });
        
        // Register query context
        $container->define(QueryContext::class, QueryContext::class);
        
        // Register services
        $container->define(IndexNameFormatter::class, IndexNameFormatter::class);
        $container->define(QueryBuilderFactory::class, QueryBuilderFactory::class);
    }
}
