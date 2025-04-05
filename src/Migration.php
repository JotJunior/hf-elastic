<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic;

use Elasticsearch\Client;
use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\Contracts\MigrationInterface;
use Jot\HfElastic\Exception\IndexExistsException;
use Jot\HfElastic\Migration\Mapping;
use Jot\HfElastic\Services\IndexNameFormatter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class Migration implements MigrationInterface
{
    public const INDEX_NAME = '';

    /**
     * Whether to add prefix to the index name.
     */
    protected bool $addPrefix = false;

    /**
     * Default settings for the index.
     */
    protected array $settings = [];

    /**
     * Index name formatter service.
     */
    protected IndexNameFormatter $indexNameFormatter;

    /**
     * Elasticsearch client instance.
     */
    private Client $client;

    /**
     * Constructor method for initializing the class with necessary dependencies.
     * @param ContainerInterface $container the container instance used to retrieve configuration and services
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $prefix = $config->get('hf_elastic.prefix', '');
        $this->settings = $config->get('hf_elastic.settings', []);
        $this->client = $container->get(ClientBuilder::class)->build();
        $this->indexNameFormatter = new IndexNameFormatter($prefix);
    }

    public function create(Mapping $index): void
    {
        $index->setName($this->parseIndexName($index->getName()));

        if ($this->exists($index->getName())) {
            throw new IndexExistsException($index->getName());
        }

        $this->client()->indices()->create($index->body());
    }

    public function parseIndexName(string $indexName): string
    {
        if ($this->addPrefix) {
            return $this->indexNameFormatter->format($indexName);
        }

        return $indexName;
    }

    public function exists(string $indexName): bool
    {
        $indexName = $this->parseIndexName($indexName);
        return $this->client()->indices()->exists(['index' => $indexName]);
    }

    public function update(Mapping $index): void
    {
        $index->setName($this->parseIndexName($index->getName()));
        $body = $index->updateBody();

        $this->client()->indices()->putMapping($body);
    }

    public function delete(string $indexName): void
    {
        $indexName = $this->parseIndexName($indexName);
        $this->client()->indices()->delete(['index' => $indexName]);
    }

    /**
     * Retrieves the client instance.
     * @return Client the client instance
     */
    protected function client(): Client
    {
        return $this->client;
    }
}
