<?php

namespace Jot\HfElastic;

use Elasticsearch\Client;
use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\Contracts\MigrationInterface;
use Jot\HfElastic\Exception\IndexExistsException;
use Jot\HfElastic\Migration\Mapping;
use Jot\HfElastic\Services\IndexNameFormatter;
use Psr\Container\ContainerInterface;

abstract class Migration implements MigrationInterface
{
    public const INDEX_NAME = '';

    /**
     * Whether to add prefix to the index name
     */
    protected bool $addPrefix = false;

    /**
     * Default settings for the index
     */
    protected array $settings = [];
    /**
     * Index name formatter service
     */
    protected IndexNameFormatter $indexNameFormatter;
    /**
     * Elasticsearch client instance
     */
    private Client $client;

    /**
     * Constructor method for initializing the class with necessary dependencies.
     * @param ContainerInterface $container The container instance used to retrieve configuration and services.
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $prefix = $config->get('hf_elastic.prefix', '');
        $this->settings = $config->get('hf_elastic.settings', []);
        $this->client = $container->get(ClientBuilder::class)->build();
        $this->indexNameFormatter = new IndexNameFormatter($prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function create(Mapping $index): void
    {
        $index->setName($this->parseIndexName($index->getName()));

        if ($this->exists($index->getName())) {
            throw new IndexExistsException();
        }

        $this->client()->indices()->create($index->body());
    }

    /**
     * {@inheritdoc}
     */
    public function parseIndexName(string $indexName): string
    {
        if ($this->addPrefix) {
            return $this->indexNameFormatter->format($indexName);
        }

        return $indexName;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $indexName): bool
    {
        $indexName = $this->parseIndexName($indexName);
        return $this->client()->indices()->exists(['index' => $indexName]);
    }

    /**
     * Retrieves the client instance.
     * @return Client The client instance.
     */
    protected function client(): Client
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Mapping $index): void
    {
        $index->setName($this->parseIndexName($index->getName()));
        $body = $index->updateBody();

        $this->client()->indices()->putMapping($body);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $indexName): void
    {
        $indexName = $this->parseIndexName($indexName);
        $this->client()->indices()->delete(['index' => $indexName]);
    }
}
