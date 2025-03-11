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
        // Arrange
        $migrationDir = '/Users/jot/Projects/Aevum/libs/hf-elastic/tests/Examples/migrations-test/elasticsearch';
        
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
        $this->assertGreaterThan(0, count($result), 'Should return at least one migration file');
        $this->assertArrayHasKey($migrationDir . '/20250120160000-create-test1.php', $result, 'Should include test1 migration file');
        $this->assertArrayHasKey($migrationDir . '/20250120160001-create-test2.php', $result, 'Should include test2 migration file');
        $this->assertArrayHasKey($migrationDir . '/20250120160002-create-users.php', $result, 'Should include users migration file');
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\AbstractCommand::getMigrationFiles
     */
    public function testGetMigrationFilesFiltersFilesByIndex(): void
    {
        // Arrange
        $migrationDir = '/Users/jot/Projects/Aevum/libs/hf-elastic/tests/Examples/migrations-test/elasticsearch';
        $indexToFilter = 'users';
        
        // Set the migrationDirectory property using reflection
        $reflection = new \ReflectionClass(AbstractCommand::class);
        $property = $reflection->getProperty('migrationDirectory');
        $property->setAccessible(true);
        $property->setValue($this->sut, $migrationDir);

        // Act
        $method = $reflection->getMethod('getMigrationFiles');
        $method->setAccessible(true);
        $result = $method->invoke($this->sut, $indexToFilter, null);

        // Assert
        $this->assertCount(1, $result, 'Should return exactly one migration file for the users index');
        $this->assertArrayHasKey($migrationDir . '/20250120160002-create-users.php', $result, 'Should include only the users migration file');
        
        // Test with a non-existent index
        $result = $method->invoke($this->sut, 'non_existent_index', null);
        $this->assertCount(0, $result, 'Should return no migration files for a non-existent index');
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\AbstractCommand::getMigrationFiles
     */
    public function testGetMigrationFilesFiltersFilesByFilename(): void
    {
        // Arrange
        $migrationDir = '/Users/jot/Projects/Aevum/libs/hf-elastic/tests/Examples/migrations-test/elasticsearch';
        $filenameToFilter = '20250120160000-create-test1.php';
        
        // Set the migrationDirectory property using reflection
        $reflection = new \ReflectionClass(AbstractCommand::class);
        $property = $reflection->getProperty('migrationDirectory');
        $property->setAccessible(true);
        $property->setValue($this->sut, $migrationDir);

        // Act
        $method = $reflection->getMethod('getMigrationFiles');
        $method->setAccessible(true);
        $result = $method->invoke($this->sut, null, $filenameToFilter);

        // Assert
        $this->assertCount(1, $result, 'Should return exactly one migration file for the specified filename');
        $this->assertArrayHasKey($migrationDir . '/' . $filenameToFilter, $result, 'Should include only the specified migration file');
        
        // Test with a non-existent filename
        $result = $method->invoke($this->sut, null, 'non_existent_file.php');
        $this->assertCount(0, $result, 'Should return no migration files for a non-existent filename');
    }
}
