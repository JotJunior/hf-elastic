<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Jot\HfElastic\ClientBuilder;
use Jot\HfElastic\Command\DestroyCommand;
use Jot\HfElastic\Command\MigrateCommand;
use Jot\HfElastic\Command\MigrationCommand;
use Jot\HfElastic\Command\ResetCommand;
use Jot\HfElastic\ConfigProvider;
use Jot\HfElastic\Contracts\MigrationInterface;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Migration;
use Jot\HfElastic\Provider\ElasticServiceProvider;
use Jot\HfElastic\Query\ElasticQueryBuilder;
use Jot\HfElastic\Query\OperatorRegistry;
use Jot\HfElastic\Query\QueryContext;
use Jot\HfElastic\Services\IndexNameFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    private ConfigProvider $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new ConfigProvider();
    }

    /**
     * @test
     * @covers \Jot\HfElastic\ConfigProvider::__invoke
     * @group unit
     * Test that the config provider returns the expected array structure
     */
    public function testInvokeReturnsExpectedArrayStructure(): void
    {
        // Arrange
        $expectedKeys = [
            'dependencies',
            'commands',
            'listeners',
            'annotations',
            'publish',
        ];

        // Act
        $result = $this->sut->__invoke();

        // Assert
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $result);
        }
    }

    /**
     * @test
     * @covers \Jot\HfElastic\ConfigProvider::__invoke
     * @group unit
     * Test that the dependencies section contains the expected interfaces
     */
    public function testDependenciesContainsExpectedServiceProvider(): void
    {
        // Arrange - This test is now checking for direct interface bindings instead of providers
        $expectedInterfaces = [
            QueryBuilderInterface::class,
            MigrationInterface::class,
            IndexNameFormatter::class,
            OperatorRegistry::class,
        ];

        // Act
        $result = $this->sut->__invoke();

        // Assert
        $this->assertArrayHasKey('dependencies', $result);
        foreach ($expectedInterfaces as $interface) {
            $this->assertArrayHasKey($interface, $result['dependencies'], "Interface $interface should be defined in dependencies");
        }
    }

    /**
     * @test
     * @covers \Jot\HfElastic\ConfigProvider::__invoke
     * @group unit
     * Test that the dependencies section contains the expected interface bindings
     */
    public function testDependenciesContainsExpectedInterfaceBindings(): void
    {
        // Arrange
        $expectedInterfaces = [
            MigrationInterface::class,
            QueryBuilderInterface::class,
        ];

        // Act
        $result = $this->sut->__invoke();

        // Assert
        foreach ($expectedInterfaces as $interface) {
            $this->assertArrayHasKey($interface, $result['dependencies']);
        }
        
        // Verify specific implementation for MigrationInterface
        $this->assertEquals(Migration::class, $result['dependencies'][MigrationInterface::class]);
        
        // Verify that QueryBuilderInterface is bound to a callable factory
        $this->assertIsCallable($result['dependencies'][QueryBuilderInterface::class]);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\ConfigProvider::__invoke
     * @group unit
     * Test that the QueryBuilderInterface factory returns a callable
     */
    public function testQueryBuilderInterfaceFactoryIsCallable(): void
    {
        // Act
        $result = $this->sut->__invoke();
        
        // Assert
        $this->assertArrayHasKey(QueryBuilderInterface::class, $result['dependencies']);
        $this->assertIsCallable($result['dependencies'][QueryBuilderInterface::class]);
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\ConfigProvider::__invoke
     * @group unit
     * Test that the QueryBuilderInterface factory uses the correct dependencies
     */
    public function testQueryBuilderInterfaceFactoryUsesDependencies(): void
    {
        // Skip this test in standard PHPUnit as we can't mock global functions
        $this->markTestSkipped('This test requires the ability to mock global functions which is not available in standard PHPUnit');
        
        // Note: In a real environment with function mocking capabilities (like uopz or runkit),
        // we would test that the factory uses ClientBuilder, IndexNameFormatter, OperatorRegistry, and QueryContext
    }

    /**
     * @test
     * @covers \Jot\HfElastic\ConfigProvider::__invoke
     * @group unit
     * Test that the commands section contains the expected commands
     */
    public function testCommandsSectionContainsExpectedCommands(): void
    {
        // Arrange
        $expectedCommands = [
            DestroyCommand::class,
            MigrateCommand::class,
            MigrationCommand::class,
            ResetCommand::class,
        ];

        // Act
        $result = $this->sut->__invoke();

        // Assert
        foreach ($expectedCommands as $command) {
            $this->assertContains($command, $result['commands']);
        }
    }

    /**
     * @test
     * @covers \Jot\HfElastic\ConfigProvider::__invoke
     * @group unit
     * Test that the annotations section contains the expected paths
     */
    public function testAnnotationsSectionContainsExpectedPaths(): void
    {
        // Arrange
        $expectedPath = __DIR__ . '/../../src';

        // Act
        $result = $this->sut->__invoke();

        // Assert
        $this->assertArrayHasKey('scan', $result['annotations']);
        $this->assertArrayHasKey('paths', $result['annotations']['scan']);
        
        // Check if the expected path is in the paths array
        $found = false;
        foreach ($result['annotations']['scan']['paths'] as $path) {
            if (realpath($path) === realpath($expectedPath)) {
                $found = true;
                break;
            }
        }
        
        $this->assertTrue($found, 'Expected path not found in annotations scan paths');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\ConfigProvider::__invoke
     * @group unit
     * Test that the publish section contains the expected configuration
     */
    public function testPublishSectionContainsExpectedConfiguration(): void
    {
        // Arrange
        $expectedSourcePath = realpath(__DIR__ . '/../../publish/hf_elastic.php');
        $expectedDestination = 'config/autoload/hf_elastic.php';

        // Act
        $result = $this->sut->__invoke();

        // Assert
        $this->assertArrayHasKey('publish', $result);
        $this->assertIsArray($result['publish']);
        $this->assertNotEmpty($result['publish']);
        
        $configPublish = null;
        foreach ($result['publish'] as $publish) {
            if ($publish['id'] === 'config') {
                $configPublish = $publish;
                break;
            }
        }
        
        $this->assertNotNull($configPublish, 'Config publish not found');
        $this->assertEquals('config', $configPublish['id']);
        $this->assertStringContainsString('hf-elastic', $configPublish['description']);
        
        // Check if the source path ends with the expected path
        $this->assertStringEndsWith('publish/hf_elastic.php', $configPublish['source']);
        
        // Check if the destination contains the expected path
        $this->assertStringEndsWith($expectedDestination, $configPublish['destination']);
    }

    /**
     * Helper method to mock global functions
     */
    private function mockFunction(string $name, callable $func): void
    {
        // This is a placeholder for function mocking
        // In a real test, you would use a library like uopz, runkit, or namespace\function
        // Since we can't actually mock functions in standard PHPUnit, this is just a stub
    }
}
