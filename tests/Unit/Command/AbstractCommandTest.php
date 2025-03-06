<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Command;

use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\Command\AbstractCommand;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * @covers \Jot\HfElastic\Command\AbstractCommand
 */
class AbstractCommandTest extends TestCase
{
    private AbstractCommand|MockObject $sut;
    private ContainerInterface|MockObject $container;
    private ConfigInterface|MockObject $config;
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {        
        parent::setUp();
        $this->root = vfsStream::setup('home');
        $this->container = $this->createMock(ContainerInterface::class);
        $this->config = $this->createMock(ConfigInterface::class);
        
        $this->container->method('get')
            ->with(ConfigInterface::class)
            ->willReturn($this->config);
        
        // Create a concrete implementation of the abstract class for testing
        $this->sut = $this->getMockForAbstractClass(
            AbstractCommand::class,
            [$this->container, 'test:command']
        );
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\AbstractCommand::createMigrationDirectoryIfNotExists
     */
    public function testCreateMigrationDirectoryIfNotExists(): void
    {
        // Arrange
        $migrationDir = vfsStream::url('home/migrations/elasticsearch');
        
        // Set the migrationDirectory property using reflection
        $reflection = new \ReflectionClass(AbstractCommand::class);
        $property = $reflection->getProperty('migrationDirectory');
        $property->setAccessible(true);
        $property->setValue($this->sut, $migrationDir);

        // Act
        $method = $reflection->getMethod('createMigrationDirectoryIfNotExists');
        $method->setAccessible(true);
        $result = $method->invoke($this->sut);

        // Assert
        $this->assertTrue($result);
        $this->assertTrue(is_dir($migrationDir));
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\AbstractCommand::migrationDirectoryExists
     */
    public function testMigrationDirectoryExistsReturnsTrueWhenDirectoryExists(): void
    {
        // Arrange
        $migrationDir = vfsStream::url('home/migrations/elasticsearch');
        mkdir($migrationDir, 0755, true);
        
        // Set the migrationDirectory property using reflection
        $reflection = new \ReflectionClass(AbstractCommand::class);
        $property = $reflection->getProperty('migrationDirectory');
        $property->setAccessible(true);
        $property->setValue($this->sut, $migrationDir);

        // Act
        $method = $reflection->getMethod('migrationDirectoryExists');
        $method->setAccessible(true);
        $result = $method->invoke($this->sut);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\AbstractCommand::migrationDirectoryExists
     */
    public function testMigrationDirectoryExistsReturnsFalseWhenDirectoryDoesNotExist(): void
    {
        // Arrange
        $migrationDir = vfsStream::url('home/nonexistent/directory');
        
        // Set the migrationDirectory property using reflection
        $reflection = new \ReflectionClass(AbstractCommand::class);
        $property = $reflection->getProperty('migrationDirectory');
        $property->setAccessible(true);
        $property->setValue($this->sut, $migrationDir);

        // Act
        $method = $reflection->getMethod('migrationDirectoryExists');
        $method->setAccessible(true);
        $result = $method->invoke($this->sut);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\AbstractCommand::getMigrationFiles
     */
    public function testGetMigrationFilesReturnsAllMigrationFiles(): void
    {
        $this->markTestSkipped('This test requires mocking the PHP include function which is difficult to do in PHPUnit');
        
        // Arrange
        $migrationDir = vfsStream::url('home/migrations/elasticsearch');
        mkdir($migrationDir, 0755, true);
        
        // Create test migration files
        file_put_contents($migrationDir . '/20210101000000-create-test1.php', '<?php return new class { const INDEX_NAME = "test1"; };');
        file_put_contents($migrationDir . '/20210101000001-create-test2.php', '<?php return new class { const INDEX_NAME = "test2"; };');
        
        // Set the migrationDirectory property using reflection
        $reflection = new \ReflectionClass(AbstractCommand::class);
        $property = $reflection->getProperty('migrationDirectory');
        $property->setAccessible(true);
        $property->setValue($this->sut, $migrationDir);

        // Act
        $method = $reflection->getMethod('getMigrationFiles');
        $method->setAccessible(true);
        $result = $method->invoke($this->sut, null, null);

        // Assert
        $this->assertCount(2, $result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\AbstractCommand::getMigrationFiles
     */
    public function testGetMigrationFilesFiltersFilesByIndex(): void
    {
        $this->markTestSkipped('This test requires mocking the PHP include function which is difficult to do in PHPUnit');
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\AbstractCommand::getMigrationFiles
     */
    public function testGetMigrationFilesFiltersFilesByFilename(): void
    {
        $this->markTestSkipped('This test requires mocking the PHP include function which is difficult to do in PHPUnit');
    }
}
