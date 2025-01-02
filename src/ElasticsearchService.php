<?php

namespace Jot\HfElastic;

use Elasticsearch\Client as ElasticsearchClient;
use Hyperf\Context\ApplicationContext;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Hyperf\Etcd\KVInterface;

/**
 * Service class for interacting with an Elasticsearch instance.
 * This class provides methods to initialize an Elasticsearch client and perform basic operations such as
 * retrieving, searching, inserting, and updating documents.
 */
class ElasticsearchService
{

    private KVInterface $etcdClient;
    private ClientBuilderFactory $clientBuilderFactory;
    private ElasticsearchClient $client;

    public function __construct(ClientBuilderFactory $clientBuilderFactory)
    {
        $this->etcdClient = ApplicationContext::getContainer()->get(KVInterface::class);
        $this->clientBuilderFactory = $clientBuilderFactory;
        $this->client = $this->createClient();
    }

    private function createClient(): \Elasticsearch\Client
    {
        $host = $this->etcdClient->get('/services/elasticsearch/host')['kvs'][0]['value'] ?? null;
        $username = $this->etcdClient->get('/services/elasticsearch/username')['kvs'][0]['value'] ?? null;
        $password = $this->etcdClient->get('/services/elasticsearch/password')['kvs'][0]['value'] ?? null;

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

    public function exists(string $id, string $index)
    {
        return $this->es()->exists([
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