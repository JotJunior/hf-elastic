<?php

declare(strict_types=1);

namespace Jot\HfElastic;

use Elasticsearch\Client as ElasticsearchClient;
use Elasticsearch\ConnectionPool\Selectors\SelectorInterface;
use Elasticsearch\ConnectionPool\SimpleConnectionPool;
use Elasticsearch\Connections\ConnectionFactoryInterface;
use Elasticsearch\Serializers\SerializerInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Jot\HfElastic\Contracts\ClientFactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service class for creating and configuring Elasticsearch clients optimized for Swoole co-routines.
 */
class ClientBuilder implements ClientFactoryInterface
{
    /**
     * @var ClientBuilderFactory Factory for creating Elasticsearch client builders.
     */
    private ClientBuilderFactory $clientBuilderFactory;

    /**
     * @var array Configuration for the Elasticsearch client.
     */
    private array $config;

    /**
     * @param ContainerInterface $container The dependency injection container.
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(private readonly ContainerInterface $container)
    {
        $this->clientBuilderFactory = $container->get(ClientBuilderFactory::class);
        $this->config = $container->get(ConfigInterface::class)->get('hf_elastic', [
            'hosts' => [],
            'username' => '',
            'password' => '',
            'retries' => 2,
            'connection_pool' => SimpleConnectionPool::class,
            'selector' => null,
            'serializer' => null,
            'connection_factory' => null,
            'endpoint' => null,
            'logger' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function build(): ElasticsearchClient
    {
        $clientBuilder = $this->clientBuilderFactory->create();

        // Apply basic configurations
        $this->configureHosts($clientBuilder);
        $this->configureAuthentication($clientBuilder);
        $this->configureSSL($clientBuilder);
        $this->configureRetries($clientBuilder);

        // Apply advanced configurations
        $this->configureConnectionPool($clientBuilder);
        $this->configureSelector($clientBuilder);
        $this->configureSerializer($clientBuilder);
        $this->configureConnectionFactory($clientBuilder);
        $this->configureLogger($clientBuilder);

        return $clientBuilder->build();
    }

    /**
     * Configures hosts for the Elasticsearch client.
     *
     * @param \Elasticsearch\ClientBuilder $clientBuilder The client builder
     */
    private function configureHosts($clientBuilder): void
    {
        $clientBuilder->setHosts($this->config['hosts']);
    }

    /**
     * Configures authentication for the Elasticsearch client.
     *
     * @param \Elasticsearch\ClientBuilder $clientBuilder The client builder
     */
    private function configureAuthentication($clientBuilder): void
    {
        if (!empty($this->config['username']) && !empty($this->config['password'])) {
            $clientBuilder->setBasicAuthentication($this->config['username'], $this->config['password']);
        }

        if (!empty($this->config['api_key']) && !empty($this->config['api_id'])) {
            $clientBuilder->setApiKey($this->config['api_key'], $this->config['api_id']);
        }
    }

    /**
     * Configures SSL options for the Elasticsearch client.
     *
     * @param \Elasticsearch\ClientBuilder $clientBuilder The client builder
     */
    private function configureSSL($clientBuilder): void
    {
        if (isset($this->config['ssl_verification'])) {
            $clientBuilder->setSSLVerification($this->config['ssl_verification']);
        }
    }

    /**
     * Configures retry attempts for the Elasticsearch client.
     *
     * @param \Elasticsearch\ClientBuilder $clientBuilder The client builder
     */
    private function configureRetries($clientBuilder): void
    {
        if (isset($this->config['retries'])) {
            $clientBuilder->setRetries((int)$this->config['retries']);
        }
    }

    /**
     * Configures connection pool for the Elasticsearch client.
     *
     * @param \Elasticsearch\ClientBuilder $clientBuilder The client builder
     */
    private function configureConnectionPool($clientBuilder): void
    {
        if (isset($this->config['connection_pool']) && class_exists($this->config['connection_pool'])) {
            $clientBuilder->setConnectionPool($this->config['connection_pool']);
        }
    }

    /**
     * Configures connection selector for the Elasticsearch client.
     *
     * @param \Elasticsearch\ClientBuilder $clientBuilder The client builder
     */
    private function configureSelector($clientBuilder): void
    {
        if (!isset($this->config['selector']) || !class_exists($this->config['selector'])) {
            return;
        }

        $selector = $this->resolveFromContainer(
            $this->config['selector'],
            SelectorInterface::class
        );

        if ($selector instanceof SelectorInterface) {
            $clientBuilder->setSelector($selector);
        }
    }

    /**
     * Resolves a class from the container or creates a new instance.
     *
     * @param string $className Class name to resolve
     * @param string|null $instanceOf Interface that the class should implement
     * @return object|null The resolved instance or null
     */
    private function resolveFromContainer(string $className, ?string $instanceOf = null): ?object
    {
        $instance = $this->container->has($className)
            ? $this->container->get($className)
            : new $className();

        if ($instanceOf !== null && !($instance instanceof $instanceOf)) {
            return null;
        }

        return $instance;
    }

    /**
     * Configures serializer for the Elasticsearch client.
     *
     * @param \Elasticsearch\ClientBuilder $clientBuilder The client builder
     */
    private function configureSerializer($clientBuilder): void
    {
        if (!isset($this->config['serializer']) || !class_exists($this->config['serializer'])) {
            return;
        }

        $serializer = $this->resolveFromContainer(
            $this->config['serializer'],
            SerializerInterface::class
        );

        if ($serializer instanceof SerializerInterface) {
            $clientBuilder->setSerializer($serializer);
        }
    }

    /**
     * Configures connection factory for the Elasticsearch client.
     *
     * @param \Elasticsearch\ClientBuilder $clientBuilder The client builder
     */
    private function configureConnectionFactory($clientBuilder): void
    {
        if (!isset($this->config['connection_factory']) || !class_exists($this->config['connection_factory'])) {
            return;
        }

        $factory = $this->resolveFromContainer(
            $this->config['connection_factory'],
            ConnectionFactoryInterface::class
        );

        if ($factory instanceof ConnectionFactoryInterface) {
            $clientBuilder->setConnectionFactory($factory);
        }
    }

    /**
     * Configures logger for the Elasticsearch client.
     *
     * @param \Elasticsearch\ClientBuilder $clientBuilder The client builder
     */
    private function configureLogger($clientBuilder): void
    {
        if (!isset($this->config['logger']) || empty($this->config['logger'])) {
            return;
        }

        $logger = $this->container->has($this->config['logger'])
            ? $this->container->get($this->config['logger'])
            : null;

        if ($logger instanceof LoggerInterface) {
            $clientBuilder->setLogger($logger);
        }
    }

}
