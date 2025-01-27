<?php

namespace Jot\HfElasticTest\QueryBuilder;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Jot\HfElastic\ClientBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Hyperf\Contract\ContainerInterface;

class ClientBuilderTest extends TestCase
{
    private MockObject|ContainerInterface $container;

    private MockObject|ClientBuilderFactory $clientBuilderFactory;

    private MockObject|ConfigInterface $config;

    protected function setUp(): void
    {
        $this->container = $this->createStub(ContainerInterface::class);
        $mockConfig = $this->createMock(ConfigInterface::class);
        $mockConfig->method('get')
            ->willReturn([
                'hosts' => [],
                'username' => '',
                'password' => '',
            ]);
        $mockClientBuilder = $this->createMock(ClientBuilderFactory::class);

        $this->container
            ->method('get')
            ->willReturnMap([
                [ClientBuilderFactory::class, $mockClientBuilder],
                [ConfigInterface::class, $mockConfig],
            ]);

    }

    public function testBuild()
    {
        $clientBuilder = new ClientBuilder($this->container);
        $this->assertInstanceOf(ClientBuilder::class, $clientBuilder);

        $client = $clientBuilder->build();
        $this->assertInstanceOf(\Elasticsearch\Client::class, $client);
    }
}

