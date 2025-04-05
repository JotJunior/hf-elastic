<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Client;

use Elasticsearch\Client;
use Jot\HfElasticCore\Contracts\ElasticClientInterface;
use Jot\HfElasticCore\Exceptions\ElasticClientException;

/**
 * Elasticsearch client wrapper implementation.
 */
class ElasticClient implements ElasticClientInterface
{
    /**
     * Elasticsearch client instance.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function createIndex(string $index, array $settings = []): array
    {
        try {
            $params = [
                'index' => $index,
                'body' => $settings,
            ];

            return $this->client->indices()->create($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to create index: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteIndex(string $index): array
    {
        try {
            $params = [
                'index' => $index,
            ];

            return $this->client->indices()->delete($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to delete index: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function indexExists(string $index): bool
    {
        try {
            $params = [
                'index' => $index,
            ];

            return $this->client->indices()->exists($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to check if index exists: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexSettings(string $index): array
    {
        try {
            $params = [
                'index' => $index,
            ];

            return $this->client->indices()->getSettings($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to get index settings: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateMapping(string $index, array $mapping): array
    {
        try {
            $params = [
                'index' => $index,
                'body' => $mapping,
            ];

            return $this->client->indices()->putMapping($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to update mapping: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMapping(string $index): array
    {
        try {
            $params = [
                'index' => $index,
            ];

            return $this->client->indices()->getMapping($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to get mapping: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function index(string $index, array $document, ?string $id = null): array
    {
        try {
            $params = [
                'index' => $index,
                'body' => $document,
            ];

            if ($id !== null) {
                $params['id'] = $id;
            }

            return $this->client->index($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to index document: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bulk(array $params): array
    {
        try {
            return $this->client->bulk($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to execute bulk operation: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $params): array
    {
        try {
            return $this->client->search($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to execute search: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $index, string $id): array
    {
        try {
            $params = [
                'index' => $index,
                'id' => $id,
            ];

            return $this->client->get($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to get document: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $index, string $id): array
    {
        try {
            $params = [
                'index' => $index,
                'id' => $id,
            ];

            return $this->client->delete($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to delete document: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(string $index, string $id, array $body): array
    {
        try {
            $params = [
                'index' => $index,
                'id' => $id,
                'body' => $body,
            ];

            return $this->client->update($params);
        } catch (\Exception $e) {
            throw new ElasticClientException('Failed to update document: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
