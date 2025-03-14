<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit;

use Elasticsearch\Client as ElasticsearchClient;
use Elasticsearch\ClientBuilder as ElasticsearchClientBuilder;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Jot\HfElastic\ClientBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Jot\HfElastic\ClientBuilder
 * @group unit
 */
class ClientBuilderTest extends TestCase
{
    private ContainerInterface|MockObject $container;
    private ConfigInterface|MockObject $config;
    private ClientBuilderFactory|MockObject $clientBuilderFactory;
    private ElasticsearchClientBuilder|MockObject $elasticsearchClientBuilder;
    private ElasticsearchClient|MockObject $elasticsearchClient;
    private ClientBuilder $clientBuilder;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->config = $this->createMock(ConfigInterface::class);
        $this->clientBuilderFactory = $this->createMock(ClientBuilderFactory::class);
        $this->elasticsearchClientBuilder = $this->createMock(ElasticsearchClientBuilder::class);
        $this->elasticsearchClient = $this->createMock(ElasticsearchClient::class);
        
        $this->container->method('get')
            ->willReturnCallback(function ($class) {
                if ($class === ClientBuilderFactory::class) {
                    return $this->clientBuilderFactory;
                } elseif ($class === ConfigInterface::class) {
                    return $this->config;
                }
                return null;
            });
            
        $this->config->expects($this->once())
            ->method('get')
            ->with('hf_elastic', [
                'hosts' => [],
                'username' => '',
                'password' => '',
            ])
            ->willReturn([
                'hosts' => ['localhost:9200'],
                'username' => 'elastic',
                'password' => 'password',
                'api_key' => 'api_key',
                'api_id' => 'api_id',
                'ssl_verification' => '/path/to/ca.pem',
            ]);
            
        $this->clientBuilder = new ClientBuilder($this->container);
    }

    /**
     * Tests the build method with all configuration options
     */
    public function testBuildWithAllOptions(): void
    {
        // Arrange
        $this->clientBuilderFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->elasticsearchClientBuilder);
            
        $this->elasticsearchClientBuilder->expects($this->once())
            ->method('setHosts')
            ->with(['localhost:9200'])
            ->willReturnSelf();
            
        $this->elasticsearchClientBuilder->expects($this->once())
            ->method('setBasicAuthentication')
            ->with('elastic', 'password')
            ->willReturnSelf();
            
        $this->elasticsearchClientBuilder->expects($this->once())
            ->method('setApiKey')
            ->with('api_key', 'api_id')
            ->willReturnSelf();
            
        $this->elasticsearchClientBuilder->expects($this->once())
            ->method('setSSLVerification')
            ->with('/path/to/ca.pem')
            ->willReturnSelf();
            
        $this->elasticsearchClientBuilder->expects($this->once())
            ->method('build')
            ->willReturn($this->elasticsearchClient);
        
        // Act
        $client = $this->clientBuilder->build();
        
        // Assert
        $this->assertSame($this->elasticsearchClient, $client);
    }

    /**
     * Tests the build method with minimal configuration
     */
    public function testBuildWithMinimalConfig(): void
    {
        // Arrange
        $minimalConfig = [
            'hosts' => ['localhost:9200'],
        ];
        
        $container = $this->createMock(ContainerInterface::class);
        $config = $this->createMock(ConfigInterface::class);
        $clientBuilderFactory = $this->createMock(ClientBuilderFactory::class);
        $elasticsearchClientBuilder = $this->createMock(ElasticsearchClientBuilder::class);
        $elasticsearchClient = $this->createMock(ElasticsearchClient::class);
        
        $container->method('get')
            ->willReturnCallback(function ($class) use ($clientBuilderFactory, $config) {
                if ($class === ClientBuilderFactory::class) {
                    return $clientBuilderFactory;
                } elseif ($class === ConfigInterface::class) {
                    return $config;
                }
                return null;
            });
            
        $config->expects($this->once())
            ->method('get')
            ->with('hf_elastic', [
                'hosts' => [],
                'username' => '',
                'password' => '',
            ])
            ->willReturn($minimalConfig);
            
        $clientBuilderFactory->expects($this->once())
            ->method('create')
            ->willReturn($elasticsearchClientBuilder);
            
        $elasticsearchClientBuilder->expects($this->once())
            ->method('setHosts')
            ->with(['localhost:9200'])
            ->willReturnSelf();
            
        $elasticsearchClientBuilder->expects($this->never())
            ->method('setBasicAuthentication');
            
        $elasticsearchClientBuilder->expects($this->never())
            ->method('setApiKey');
            
        $elasticsearchClientBuilder->expects($this->never())
            ->method('setSSLVerification');
            
        $elasticsearchClientBuilder->expects($this->once())
            ->method('build')
            ->willReturn($elasticsearchClient);
            
        $clientBuilder = new ClientBuilder($container);
        
        // Act
        $client = $clientBuilder->build();
        
        // Assert
        $this->assertSame($elasticsearchClient, $client);
    }
}
