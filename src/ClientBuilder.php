<?php

declare(strict_types=1);

namespace Jot\HfElastic;

use Elasticsearch\Client as ElasticsearchClient;
use Elasticsearch\Namespaces\IndicesNamespace;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Hyperf\Etcd\KVInterface;

/**
 * Service class for interacting with Elasticsearch through a configured client.
 */
class ClientBuilder
{

    private KVInterface $etcdClient;
    private ClientBuilderFactory $clientBuilderFactory;

    public function __construct(KVInterface $etcdClient, ClientBuilderFactory $clientBuilderFactory)
    {
        $this->etcdClient = $etcdClient;
        $this->clientBuilderFactory = $clientBuilderFactory;
    }

    public function build(): ElasticsearchClient
    {
        $host = $this->etcdClient->get('/services/elasticsearch/host')['kvs'][0]['value'] ?? null;
        $username = $this->etcdClient->get('/services/elasticsearch/username')['kvs'][0]['value'] ?? null;
        $password = $this->etcdClient->get('/services/elasticsearch/password')['kvs'][0]['value'] ?? null;

        $clientBuilder = $this->clientBuilderFactory->create();
        $clientBuilder->setHosts([(string)$host])
            ->setBasicAuthentication((string)$username, (string)$password);

        return $clientBuilder->build();
    }

}