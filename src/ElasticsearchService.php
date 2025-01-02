<?php

namespace Jot\HfElastic;

use Elasticsearch\Client;
use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Etcd\Client as EtcdClient;
use Hyperf\Elasticsearch\ClientBuilderFactory;

/**
 * Service class for interacting with an Elasticsearch instance.
 * This class provides methods to initialize an Elasticsearch client and perform basic operations such as
 * retrieving, searching, inserting, and updating documents.
 */
class ElasticsearchService
{

    private EtcdClient $etcdClient;
    private ClientBuilderFactory $clientBuilderFactory;
    private Client $client;

    public function __construct(EtcdClient $etcdClient, ClientBuilderFactory $clientBuilderFactory)
    {
        $this->etcdClient = $etcdClient;
        $this->clientBuilderFactory = $clientBuilderFactory;
        $this->client = $this->createClient();
    }

    private function createClient(): \Elasticsearch\Client
    {
        $host = $this->etcdClient->get('/services/elasticsearch/host');
        $username = $this->etcdClient->get('/services/elasticsearch/username');
        $password = $this->etcdClient->get('/services/elasticsearch/password');

        $clientBuilder = $this->clientBuilderFactory->create();
        $clientBuilder->setHosts([(string)$host])
            ->setBasicAuthentication((string)$username, (string)$password);

        return $clientBuilder->build();
    }

    public function es()
    {
        return $this->client;
    }

    public function get(string $id, string $index)
    {
        return $this->es()->get([
            'index' => $index,
            'id' => $id
        ]);
    }

    public function search(array $params, string $index = null)
    {
        return $this->es()->search([
            'index' => $index,
            ...$params
        ]);
    }

    public function insert(array $body, string $id, string $index = null)
    {
        return $this->es()->index([
            'index' => $index,
            'id' => $id,
            'body' => $body,
        ]);
    }

    public function update(array $body, string $id, string $index = null)
    {
        $data = $this->get($id, $index);
        $body = array_merge($data['_source'], $body);

        return $this->es()->index([
            'index' => $index,
            'id' => $id,
            'body' => $body
        ]);
    }
}