<?php

declare(strict_types=1);

namespace Jot\HfElastic;

use Elasticsearch\Client as ElasticsearchClient;
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

    public function __construct(KVInterface $etcdClient, ClientBuilderFactory $clientBuilderFactory)
    {
        $this->etcdClient = $etcdClient;
        $this->clientBuilderFactory = $clientBuilderFactory;
        $this->client = $this->createClient();
    }

    /**
     * Creates and configures an Elasticsearch client using credentials and host details
     * retrieved from the etcd key-value store.
     *
     * @return ElasticsearchClient The configured Elasticsearch client instance.
     */
    private function createClient(): ElasticsearchClient
    {
        $host = $this->etcdClient->get('/services/elasticsearch/host')['kvs'][0]['value'] ?? null;
        $username = $this->etcdClient->get('/services/elasticsearch/username')['kvs'][0]['value'] ?? null;
        $password = $this->etcdClient->get('/services/elasticsearch/password')['kvs'][0]['value'] ?? null;

        $clientBuilder = $this->clientBuilderFactory->create();
        $clientBuilder->setHosts([(string)$host])
            ->setBasicAuthentication((string)$username, (string)$password);

        return $clientBuilder->build();
    }

    /**
     * Returns the Elasticsearch client instance.
     *
     * @return ElasticsearchClient The configured Elasticsearch client.
     */
    public function es(): ElasticsearchClient
    {
        return $this->client;
    }

    /**
     * Retrieves a document from the specified index using its ID.
     *
     * @param string $id The unique identifier of the document to retrieve.
     * @param string $index The name of the index from which to retrieve the document.
     * @return array Returns the document data as an associative array.
     */
    public function get(string $id, string $index): array
    {
        return $this->es()->get([
            'index' => $index,
            'id' => $id
        ]);
    }

    /**
     * Checks whether a document exists in the specified index using its ID.
     *
     * @param string $id The unique identifier of the document to check.
     * @param string $index The name of the index where the document is expected to exist.
     * @return bool Returns true if the document exists, false otherwise.
     */
    public function exists(string $id, string $index): bool
    {
        return $this->es()->exists([
            'index' => $index,
            'id' => $id
        ]);
    }

    /**
     * Executes a search query using the provided parameters and index.
     *
     * @param array $params The search parameters to be used in the query.
     * @param string|null $index The index to search in, or null to search in the default index.
     * @return array|callable The result of the search query, which could be an array or a callable.
     */
    public function search(array $params, string $index = null): array|callable
    {
        return $this->es()->search([
            'index' => $index,
            ...$params
        ]);
    }

    /**
     * Inserts a document into an index with the specified ID and body.
     *
     * @param array $body The content of the document to be inserted.
     * @param string $id The unique identifier for the document.
     * @param string|null $index The target index for the document, or null to use the default index.
     * @return array|callable The result of the operation, which could be an array or a callable.
     */
    public function insert(array $body, string $id, string $index = null): array|callable
    {
        return $this->es()->index([
            'index' => $index,
            'id' => $id,
            'body' => $body,
        ]);
    }

    /**
     * Updates a document in the specified index with the provided data.
     *
     * @param array $body The data to update the document with.
     * @param string $id The ID of the document to be updated.
     * @param string|null $index The index containing the document, or null for the default index.
     * @return array|callable The result of the update operation, which could be an array or a callable.
     */
    public function update(array $body, string $id, string $index = null): array|callable
    {
        $data = $this->get($id, $index);
        $body = array_merge($data['_source'], $body);

        return $this->es()->index([
            'index' => $index,
            'id' => $id,
            'body' => $body
        ]);
    }

    /**
     * Deletes a document from the specified index using its ID.
     *
     * @param string $id The ID of the document to be deleted.
     * @param string|null $index The index from which the document should be deleted, or null to use the default index.
     * @return array|callable The response from the delete operation, which could be an array or a callable.
     */
    public function delete(string $id, string $index = null): array|callable
    {
        return $this->es()->delete([
            'index' => $index,
            'id' => $id
        ]);
    }

}