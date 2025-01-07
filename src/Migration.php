<?php

namespace Jot\HfElastic;

use Hyperf\Di\Annotation\Inject;
use Jot\HfElastic\Migration\Mapping;

abstract class Migration
{
    public const INDEX_NAME = '';

    protected ElasticsearchService $client;

    public function setClient(ElasticsearchService $client): void
    {
        $this->client = $client;
    }

    protected function client(): ElasticsearchService
    {
        return $this->client;
    }

    protected function create(Mapping $index): void
    {
        $this->client()->indices()->create($index->body());
    }

    protected function update(Mapping $index): void
    {
        $this->client()->indices()->putMapping($index->body());
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