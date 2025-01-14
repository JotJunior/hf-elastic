<?php

namespace Jot\HfElastic;

use Elasticsearch\Client;
use Jot\HfElastic\Migration\Mapping;

abstract class Migration
{
    public const INDEX_NAME = '';

    protected Client $client;

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    protected function client(): Client
    {
        return $this->client;
    }

    protected function create(Mapping $index): void
    {
        if ($this->exists($index->getName())) {
            throw new \Exception('Index already exists');
        }
        $this->client()->indices()->create($index->body());
    }

    protected function update(Mapping $index): void
    {
        $body = $index->updateBody();
        $this->client()->indices()->putMapping($body);
    }

    public function delete(string $indexName): void
    {
        $this->client()->indices()->delete(['index' => $indexName]);
    }

    public function exists(string $indexName): bool
    {
        return $this->client()->indices()->exists(['index' => $indexName]);
    }

}