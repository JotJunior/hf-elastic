<?php

namespace Jot\HfElasticTest\QueryBuilder;

use Elasticsearch\Client;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Jot\HfElastic\ClientBuilder;
use Jot\HfElastic\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;

trait QueryBuilderTestTrait
{

    protected function createMockClientFromClientBuilder(): MockObject
    {
        $mockClient = $this->createStub(Client::class);
        return $this->createConfiguredMock(\Elasticsearch\ClientBuilder::class, [
            'build' => $mockClient,
        ]);
    }

    protected function createQueryBuilderWithMocks(array $mockHits = null): QueryBuilder
    {
        $mockClient = $this->createMockClientFromClientBuilder()->build();
        $mockClient->method('search')->willReturn(['hits' => ['hits' => $mockHits]]);

        $mockClientBuilder = $this->createConfiguredMock(\Elasticsearch\ClientBuilder::class, [
            'build' => $mockClient,
        ]);
        $mockConfig = $this->createConfiguredMock(ConfigInterface::class, [
            'get' => ['prefix' => ''],
        ]);

        $mockContainer = $this->createStub(ContainerInterface::class);
        $mockContainer->method('get')->willReturnMap([
            [ClientBuilder::class, $mockClientBuilder],
            [ConfigInterface::class, $mockConfig],
        ]);

        return new QueryBuilder($mockContainer);
    }

}