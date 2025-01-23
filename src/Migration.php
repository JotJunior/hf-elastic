<?php

namespace Jot\HfElastic;

use Elasticsearch\Client;
use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\Migration\Mapping;
use Psr\Container\ContainerInterface;

abstract class Migration
{

    public const INDEX_NAME = '';
    protected bool $addPrefix = false;
    protected array $settings = [];
    protected string $prefix = '';
    protected Client $client;

    public function __construct(ContainerInterface $container)
    {
        $this->prefix = $container->get(ConfigInterface::class)->get('hf_elastic.prefix', '');
        $this->settings = $container->get(ConfigInterface::class)->get('hf_elastic.settings', []);
        $this->client = $container->get(ClientBuilder::class)->build();
    }

    /**
     * Retrieves the client instance.
     *
     * @return Client The client instance.
     */
    protected function client(): Client
    {
        return $this->client;
    }

    /**
     * Parses and returns the fully qualified index name by adding a prefix if necessary.
     *
     * @param string $indexName The original name of the index to be parsed.
     * @return string The parsed index name, including the prefix if applicable.
     */
    protected function parseIndexName(string $indexName): string
    {
        if ($this->addPrefix && !str_starts_with($indexName, $this->prefix)) {
            $indexName = sprintf('%s_%s', $this->prefix, $indexName);
        }
        return $indexName;
    }

    /**
     * Creates a new index in the Elasticsearch cluster based on the provided mapping.
     *
     * @param Mapping $index The mapping object containing the index configuration. The index name will be parsed and validated.
     * @return void
     *
     * @throws \Exception If the index already exists.
     */
    protected function create(Mapping $index): void
    {
        $index->setName($this->parseIndexName($index->getName()));
        if ($this->exists($index->getName())) {
            throw new \Exception('Index already exists');
        }
        $this->client()->indices()->create($index->body());
    }

    /**
     * Updates the mapping of the specified index in the Elasticsearch cluster.
     *
     * @param Mapping $index The index mapping object to update. The index name will be parsed and the update body prepared before applying changes.
     * @return void
     */
    protected function update(Mapping $index): void
    {
        $index->setName($this->parseIndexName($index->getName()));
        $body = $index->updateBody();
        $this->client()->indices()->putMapping($body);
    }

    /**
     * Deletes the specified index from the Elasticsearch cluster.
     *
     * @param string $indexName The name of the index to delete. It will be parsed before deletion.
     * @return void
     */
    public function delete(string $indexName): void
    {
        $indexName = $this->parseIndexName($indexName);
        $this->client()->indices()->delete(['index' => $indexName]);
    }

    /**
     * Checks if the specified index exists.
     *
     * @param string $indexName The name of the index to check.
     * @return bool Returns true if the index exists, false otherwise.
     */
    public function exists(string $indexName): bool
    {
        $indexName = $this->parseIndexName($indexName);
        return $this->client()->indices()->exists(['index' => $indexName]);
    }

}