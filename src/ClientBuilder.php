<?php

declare(strict_types=1);

namespace Jot\HfElastic;

use Elasticsearch\Client as ElasticsearchClient;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Jot\HfElastic\Contracts\ClientFactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * Service class for creating and configuring Elasticsearch clients.
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
     */
    public function __construct(ContainerInterface $container)
    {
        $this->clientBuilderFactory = $container->get(ClientBuilderFactory::class);
        $this->config = $container->get(ConfigInterface::class)->get('hf_elastic', [
            'hosts' => [],
            'username' => '',
            'password' => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function build(): ElasticsearchClient
    {
        $clientBuilder = $this->clientBuilderFactory->create();
        $clientBuilder->setHosts($this->config['hosts']);
        
        if (!empty($this->config['username']) && !empty($this->config['password'])) {
            $clientBuilder->setBasicAuthentication($this->config['username'], $this->config['password']);
        }
        
        if (!empty($this->config['api_key']) && !empty($this->config['api_id'])) {
            $clientBuilder->setApiKey($this->config['api_key'], $this->config['api_id']);
        }
        
        if (!empty($this->config['ssl_verification'])) {
            $clientBuilder->setSSLVerification($this->config['ssl_verification']);
        }
        
        return $clientBuilder->build();
    }
}
