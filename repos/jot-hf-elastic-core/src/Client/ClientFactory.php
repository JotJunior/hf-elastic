<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Client;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Hyperf\Contract\ConfigInterface;
use Jot\HfElasticCore\Contracts\ClientFactoryInterface;

/**
 * Factory for creating Elasticsearch client instances.
 */
class ClientFactory implements ClientFactoryInterface
{
    /**
     * Config instance.
     *
     * @var ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * Constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function createClient(): Client
    {
        $config = $this->config->get('hf_elastic_core', []);
        $clientConfig = $config['client'] ?? [];

        $clientBuilder = ClientBuilder::create();

        // Configure hosts
        if (isset($clientConfig['hosts']) && is_array($clientConfig['hosts'])) {
            $clientBuilder->setHosts($clientConfig['hosts']);
        }

        // Configure retries
        if (isset($clientConfig['retries']) && is_int($clientConfig['retries'])) {
            $clientBuilder->setRetries($clientConfig['retries']);
        }

        // Configure connection pool
        if (isset($clientConfig['connection_pool'])) {
            $clientBuilder->setConnectionPool($clientConfig['connection_pool']);
        }

        // Configure connection selector
        if (isset($clientConfig['selector'])) {
            $clientBuilder->setSelector($clientConfig['selector']);
        }

        // Configure serializer
        if (isset($clientConfig['serializer'])) {
            $clientBuilder->setSerializer($clientConfig['serializer']);
        }

        // Configure logger
        if (isset($clientConfig['logger'])) {
            $clientBuilder->setLogger($clientConfig['logger']);
        }

        // Configure tracer
        if (isset($clientConfig['tracer'])) {
            $clientBuilder->setTracer($clientConfig['tracer']);
        }

        // Configure SSL verification
        if (isset($clientConfig['ssl_verification']) && is_bool($clientConfig['ssl_verification'])) {
            $clientBuilder->setSSLVerification($clientConfig['ssl_verification']);
        }

        // Configure HTTP handler
        if (isset($clientConfig['handler'])) {
            $clientBuilder->setHandler($clientConfig['handler']);
        }

        // Configure connection factory
        if (isset($clientConfig['connection_factory'])) {
            $clientBuilder->setConnectionFactory($clientConfig['connection_factory']);
        }

        // Configure endpoint
        if (isset($clientConfig['endpoint'])) {
            $clientBuilder->setEndpoint($clientConfig['endpoint']);
        }

        return $clientBuilder->build();
    }
}
