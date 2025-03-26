<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit;

use Elasticsearch\Client as ElasticsearchClient;
use Elasticsearch\ClientBuilder as ElasticsearchClientBuilder;
use Elasticsearch\ConnectionPool\SimpleConnectionPool;
use Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector;
use Elasticsearch\ConnectionPool\Selectors\SelectorInterface;
use Elasticsearch\Connections\ConnectionFactoryInterface;
use Elasticsearch\Serializers\SerializerInterface;
use Elasticsearch\Serializers\SmartSerializer;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Hyperf\Engine\Http\Client as SwooleClient;
use Jot\HfElastic\ClientBuilder;
use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionMethod;

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
                'retries' => 2,
                'connection_pool' => \Elasticsearch\ConnectionPool\SimpleConnectionPool::class,
                'selector' => null,
                'serializer' => null,
                'connection_factory' => null,
                'endpoint' => null,
                'logger' => null,
            ])
            ->willReturn([
                'hosts' => ['localhost:9200'],
                'username' => 'elastic',
                'password' => 'password',
                'api_key' => 'api_key',
                'api_id' => 'api_id',
                'ssl_verification' => '/path/to/ca.pem',
                'retries' => 3,
            ]);
            
        $this->clientBuilder = new ClientBuilder($this->container);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
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
            ->method('setRetries')
            ->with(3)
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
                'retries' => 2,
                'connection_pool' => \Elasticsearch\ConnectionPool\SimpleConnectionPool::class,
                'selector' => null,
                'serializer' => null,
                'connection_factory' => null,
                'endpoint' => null,
                'logger' => null,
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
    
    /**
     * Tests the configureConnectionPool method
     */
    public function testConfigureConnectionPool(): void
    {
        // Arrange
        $config = [
            'hosts' => ['localhost:9200'],
            'connection_pool' => SimpleConnectionPool::class,
        ];
        
        // Create new mocks for this test to avoid conflicts with setUp
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
        
        $config->method('get')
            ->willReturn([
                'hosts' => ['localhost:9200'],
                'connection_pool' => SimpleConnectionPool::class,
            ]);
        
        $clientBuilderFactory->expects($this->once())
            ->method('create')
            ->willReturn($elasticsearchClientBuilder);
        
        $elasticsearchClientBuilder->expects($this->once())
            ->method('setConnectionPool')
            ->with(SimpleConnectionPool::class)
            ->willReturnSelf();
        
        $elasticsearchClientBuilder->expects($this->once())
            ->method('build')
            ->willReturn($elasticsearchClient);
        
        $clientBuilder = new ClientBuilder($container);
        
        // Act
        $client = $clientBuilder->build();
        
        // Assert
        $this->assertSame($elasticsearchClient, $client);
    }
    
    /**
     * Tests the configureSelector method
     */
    public function testConfigureSelector(): void
    {
        // Create new mocks for this test to avoid conflicts with setUp
        $container = $this->createMock(ContainerInterface::class);
        $config = $this->createMock(ConfigInterface::class);
        $clientBuilderFactory = $this->createMock(ClientBuilderFactory::class);
        $elasticsearchClientBuilder = $this->createMock(ElasticsearchClientBuilder::class);
        $elasticsearchClient = $this->createMock(ElasticsearchClient::class);
        $selector = $this->createMock(SelectorInterface::class);
        
        // Configure the container
        $container->method('get')
            ->willReturnCallback(function ($class) use ($clientBuilderFactory, $config, $selector) {
                if ($class === ClientBuilderFactory::class) {
                    return $clientBuilderFactory;
                } elseif ($class === ConfigInterface::class) {
                    return $config;
                } elseif ($class === RoundRobinSelector::class) {
                    return $selector;
                }
                return null;
            });
        
        $container->method('has')
            ->with(RoundRobinSelector::class)
            ->willReturn(true);
        
        $config->method('get')
            ->willReturn([
                'hosts' => ['localhost:9200'],
                'selector' => RoundRobinSelector::class,
            ]);
        
        $clientBuilderFactory->expects($this->once())
            ->method('create')
            ->willReturn($elasticsearchClientBuilder);
        
        $elasticsearchClientBuilder->expects($this->once())
            ->method('setSelector')
            ->with($selector)
            ->willReturnSelf();
        
        $elasticsearchClientBuilder->expects($this->once())
            ->method('build')
            ->willReturn($elasticsearchClient);
        
        $clientBuilder = new ClientBuilder($container);
        
        // Act
        $client = $clientBuilder->build();
        
        // Assert
        $this->assertSame($elasticsearchClient, $client);
    }
    
    /**
     * Tests the configureSerializer method
     */
    public function testConfigureSerializer(): void
    {
        // Create new mocks for this test to avoid conflicts with setUp
        $container = $this->createMock(ContainerInterface::class);
        $config = $this->createMock(ConfigInterface::class);
        $clientBuilderFactory = $this->createMock(ClientBuilderFactory::class);
        $elasticsearchClientBuilder = $this->createMock(ElasticsearchClientBuilder::class);
        $elasticsearchClient = $this->createMock(ElasticsearchClient::class);
        $serializer = $this->createMock(SerializerInterface::class);
        
        // Configure the container
        $container->method('get')
            ->willReturnCallback(function ($class) use ($clientBuilderFactory, $config, $serializer) {
                if ($class === ClientBuilderFactory::class) {
                    return $clientBuilderFactory;
                } elseif ($class === ConfigInterface::class) {
                    return $config;
                } elseif ($class === SmartSerializer::class) {
                    return $serializer;
                }
                return null;
            });
        
        $container->method('has')
            ->with(SmartSerializer::class)
            ->willReturn(true);
        
        $config->method('get')
            ->willReturn([
                'hosts' => ['localhost:9200'],
                'serializer' => SmartSerializer::class,
            ]);
        
        $clientBuilderFactory->expects($this->once())
            ->method('create')
            ->willReturn($elasticsearchClientBuilder);
        
        $elasticsearchClientBuilder->expects($this->once())
            ->method('setSerializer')
            ->with($serializer)
            ->willReturnSelf();
        
        $elasticsearchClientBuilder->expects($this->once())
            ->method('build')
            ->willReturn($elasticsearchClient);
        
        $clientBuilder = new ClientBuilder($container);
        
        // Act
        $client = $clientBuilder->build();
        
        // Assert
        $this->assertSame($elasticsearchClient, $client);
    }
    
    /**
     * Tests the configureConnectionFactory method
     */
    public function testConfigureConnectionFactory(): void
    {
        // Create a mock class for ConnectionFactoryInterface if it doesn't exist
        if (!class_exists('CustomConnectionFactory')) {
            eval('class CustomConnectionFactory implements \\Elasticsearch\\Connections\\ConnectionFactoryInterface {
                public function create(array $hostDetails): \\Elasticsearch\\Connections\\ConnectionInterface {
                    return new class() implements \\Elasticsearch\\Connections\\ConnectionInterface {
                        public function getLastRequestInfo() {}
                        public function performRequest($method, $uri, $params = null, $body = null, array $options = [], \\Elasticsearch\\Transport $transport = null) {}
                        public function getTransportSchema(): string { return "http"; }
                        public function getHost(): array { return []; }
                        public function getUserPass(): ?string { return null; }
                        public function getPath(): ?string { return null; }
                    };
                }
            }');
        }
        
        // Create new mocks for this test to avoid conflicts with setUp
        $container = $this->createMock(ContainerInterface::class);
        $config = $this->createMock(ConfigInterface::class);
        $clientBuilderFactory = $this->createMock(ClientBuilderFactory::class);
        $elasticsearchClientBuilder = $this->createMock(ElasticsearchClientBuilder::class);
        $elasticsearchClient = $this->createMock(ElasticsearchClient::class);
        $factory = $this->createMock(ConnectionFactoryInterface::class);
        
        // Configure the container
        $container->method('get')
            ->willReturnCallback(function ($class) use ($clientBuilderFactory, $config, $factory) {
                if ($class === ClientBuilderFactory::class) {
                    return $clientBuilderFactory;
                } elseif ($class === ConfigInterface::class) {
                    return $config;
                } elseif ($class === 'CustomConnectionFactory') {
                    return $factory;
                }
                return null;
            });
        
        $container->method('has')
            ->with('CustomConnectionFactory')
            ->willReturn(true);
        
        $config->method('get')
            ->willReturn([
                'hosts' => ['localhost:9200'],
                'connection_factory' => 'CustomConnectionFactory',
            ]);
        
        $clientBuilderFactory->expects($this->once())
            ->method('create')
            ->willReturn($elasticsearchClientBuilder);
        
        $elasticsearchClientBuilder->expects($this->once())
            ->method('setConnectionFactory')
            ->with($factory)
            ->willReturnSelf();
        
        $elasticsearchClientBuilder->expects($this->once())
            ->method('build')
            ->willReturn($elasticsearchClient);
        
        $clientBuilder = new ClientBuilder($container);
        
        // Act
        $client = $clientBuilder->build();
        
        // Assert
        $this->assertSame($elasticsearchClient, $client);
    }
    
    /**
     * Tests the configureLogger method
     */
    public function testConfigureLogger(): void
    {
        // Create new mocks for this test to avoid conflicts with setUp
        $container = $this->createMock(ContainerInterface::class);
        $config = $this->createMock(ConfigInterface::class);
        $clientBuilderFactory = $this->createMock(ClientBuilderFactory::class);
        $elasticsearchClientBuilder = $this->createMock(ElasticsearchClientBuilder::class);
        $elasticsearchClient = $this->createMock(ElasticsearchClient::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        // Configure the container
        $container->method('get')
            ->willReturnCallback(function ($class) use ($clientBuilderFactory, $config, $logger) {
                if ($class === ClientBuilderFactory::class) {
                    return $clientBuilderFactory;
                } elseif ($class === ConfigInterface::class) {
                    return $config;
                } elseif ($class === 'app.logger') {
                    return $logger;
                }
                return null;
            });
        
        $container->method('has')
            ->with('app.logger')
            ->willReturn(true);
        
        $config->method('get')
            ->willReturn([
                'hosts' => ['localhost:9200'],
                'logger' => 'app.logger',
            ]);
        
        $clientBuilderFactory->expects($this->once())
            ->method('create')
            ->willReturn($elasticsearchClientBuilder);
        
        $elasticsearchClientBuilder->expects($this->once())
            ->method('setLogger')
            ->with($logger)
            ->willReturnSelf();
        
        $elasticsearchClientBuilder->expects($this->once())
            ->method('build')
            ->willReturn($elasticsearchClient);
        
        $clientBuilder = new ClientBuilder($container);
        
        // Act
        $client = $clientBuilder->build();
        
        // Assert
        $this->assertSame($elasticsearchClient, $client);
    }
    
    /**
     * Tests the resolveFromContainer method
     */
    public function testResolveFromContainer(): void
    {
        // Create a new container mock for this test to avoid conflicts with setUp
        $container = $this->createMock(ContainerInterface::class);
        $clientBuilderFactory = $this->createMock(ClientBuilderFactory::class);
        $config = $this->createMock(ConfigInterface::class);
        
        // Configure the container to return the necessary dependencies
        $container->method('get')
            ->willReturnCallback(function ($class) use ($clientBuilderFactory, $config) {
                if ($class === ClientBuilderFactory::class) {
                    return $clientBuilderFactory;
                } elseif ($class === ConfigInterface::class) {
                    return $config;
                } elseif ($class === 'TestClass') {
                    return new \stdClass();
                }
                return null;
            });
            
        $config->method('get')
            ->willReturn([
                'hosts' => ['localhost:9200'],
            ]);
        
        $clientBuilder = new ClientBuilder($container);
        
        // We need to use reflection to test this private method
        $reflectionClass = new ReflectionClass(ClientBuilder::class);
        $method = $reflectionClass->getMethod('resolveFromContainer');
        $method->setAccessible(true);
        
        // Test when class exists in container
        $container->method('has')
            ->with('TestClass')
            ->willReturn(true);
        
        $result = $method->invoke($clientBuilder, 'TestClass');
        $this->assertInstanceOf(\stdClass::class, $result);
    }
    
    /**
     * Tests the resolveFromContainer method when class doesn't exist in container
     */
    public function testResolveFromContainerWithInstantiation(): void
    {
        // Create a new container mock for this test
        $container = $this->createMock(ContainerInterface::class);
        $clientBuilderFactory = $this->createMock(ClientBuilderFactory::class);
        $config = $this->createMock(ConfigInterface::class);
        
        // Configure the container to return the necessary dependencies
        $container->method('get')
            ->willReturnCallback(function ($class) use ($clientBuilderFactory, $config) {
                if ($class === ClientBuilderFactory::class) {
                    return $clientBuilderFactory;
                } elseif ($class === ConfigInterface::class) {
                    return $config;
                }
                return null;
            });
            
        $config->method('get')
            ->willReturn([
                'hosts' => ['localhost:9200'],
            ]);
        
        $clientBuilder = new ClientBuilder($container);
        
        // We need to use reflection to test this private method
        $reflectionClass = new ReflectionClass(ClientBuilder::class);
        $method = $reflectionClass->getMethod('resolveFromContainer');
        $method->setAccessible(true);
        
        // Test when class doesn't exist in container but can be instantiated
        $container->method('has')
            ->with(\stdClass::class)
            ->willReturn(false);
            
        $result = $method->invoke($clientBuilder, \stdClass::class);
        $this->assertInstanceOf(\stdClass::class, $result);
    }
    
    /**
     * Tests the resolveFromContainer method with type checking
     */
    public function testResolveFromContainerWithTypeCheck(): void
    {
        // We need to use reflection to test this private method
        $reflectionClass = new ReflectionClass(ClientBuilder::class);
        $method = $reflectionClass->getMethod('resolveFromContainer');
        $method->setAccessible(true);
        
        // Test when class exists in container but doesn't implement the required interface
        $this->container->method('has')
            ->with('InvalidClass')
            ->willReturn(true);
            
        $invalidObject = new \stdClass();
        $this->container->method('get')
            ->with('InvalidClass')
            ->willReturn($invalidObject);
            
        $result = $method->invoke($this->clientBuilder, 'InvalidClass', SerializerInterface::class);
        $this->assertNull($result);
    }
    
    // O teste para createSwooleHandler foi removido porque o mu00e9todo nu00e3o existe mais na classe ClientBuilder
    
    // Os testes para configureSwooleHandler e createSwooleHandlerExceptionHandling foram removidos
    // porque esses métodos não existem mais na classe ClientBuilder
}
