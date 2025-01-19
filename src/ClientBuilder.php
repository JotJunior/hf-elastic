<?php

declare(strict_types=1);

namespace Jot\HfElastic;

use Elasticsearch\Client as ElasticsearchClient;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Psr\Container\ContainerInterface;

/**
 * Service class for interacting with Elasticsearch through a configured client.
 */
class ClientBuilder
{

    private ClientBuilderFactory $clientBuilderFactory;

    private array $config;

    public function __construct(ContainerInterface $container)
    {
        $this->clientBuilderFactory = $container->get(ClientBuilderFactory::class);
        $this->config = $container->get(ConfigInterface::class)->get('elasticsearch');
    }

    public function build(): ElasticsearchClient
    {
        $clientBuilder = $this->clientBuilderFactory->create();
        $clientBuilder->setHosts($this->config['hosts']);
        if (!empty($this->config['username']) && !empty($this->config['password'])) {
            $clientBuilder->setBasicAuthentication($this->config['username'], $this->config['password']);
        }
        if (!empty($this->config['api_key']) && !empty($this->config['api_key_id'])) {
            $clientBuilder->setApiKey($this->config['api_key'], $this->config['api_key_id']);
        }
        if (!empty($this->config['ssl_verification'])) {
            $clientBuilder->setSSLVerification($this->config['ssl_verification']);
        }
        if (!empty($this->config['ssl_verification'])) {
            $clientBuilder->setSSLVerification($this->config['ssl_verification']);
        }
        return $clientBuilder->build();
    }

}