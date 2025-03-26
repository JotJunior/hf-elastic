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
 * @group unit
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
     * @covers \Jot\HfElastic\Command\AbstractCommand::createMigrationDirectoryIfNotExists
     * @group unit
     * Test that the migration directory is created if it does not exist
     * What is being tested:
     * - The createMigrationDirectoryIfNotExists method of the AbstractCommand class
     * Conditions/Scenarios:
     * - The migration directory does not exist
     * Expected results:
     * - The method should return true
     * - The directory should be created
     * @return void
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
        $this->assertTrue($result, 'Method should return true when directory is created');
        $this->assertTrue(is_dir($migrationDir), 'Directory should be created');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Command\AbstractCommand::migrationDirectoryExists
     * @group unit
     * Test that migrationDirectoryExists returns true when the directory exists
     * What is being tested:
     * - The migrationDirectoryExists method of the AbstractCommand class
     * Conditions/Scenarios:
     * - The migration directory exists
     * Expected results:
     * - The method should return true
     * @return void
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
        $this->assertTrue($result, 'Method should return true when directory exists');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Command\AbstractCommand::migrationDirectoryExists
     * @group unit
     * Test that migrationDirectoryExists returns false when the directory does not exist
     * What is being tested:
     * - The migrationDirectoryExists method of the AbstractCommand class
     * Conditions/Scenarios:
     * - The migration directory does not exist
     * Expected results:
     * - The method should return false
     * @return void
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
        $this->assertFalse($result, 'Method should return false when directory does not exist');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Command\AbstractCommand::getMigrationFiles
     * @group unit
     * Test that getMigrationFiles returns all migration files when no filters are applied
     * What is being tested:
     * - The getMigrationFiles method of the AbstractCommand class
     * Conditions/Scenarios:
     * - No index or filename filters are applied
     * Expected results:
     * - The method should return all migration files in the directory
     * - The result should include test1, test2, and users migration files
     * @return void
     */
    public function testGetMigrationFilesReturnsAllMigrationFiles(): void
    {
        // Arrange
        $migrationDir = '/Users/jot/Projects/Jot/libs/hf-elastic/tests/Examples/migrations-test/elasticsearch';
        
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
     * @covers \Jot\HfElastic\Command\AbstractCommand::getMigrationFiles
     * @group unit
     * Test that getMigrationFiles correctly filters files by index name
     * What is being tested:
     * - The getMigrationFiles method of the AbstractCommand class with index filter
     * Conditions/Scenarios:
     * - Filtering migration files by index name 'users'
     * - Filtering migration files by a non-existent index name
     * Expected results:
     * - When filtering by 'users', only the users migration file should be returned
     * - When filtering by a non-existent index, no files should be returned
     * @return void
     */
    public function testGetMigrationFilesFiltersFilesByIndex(): void
    {
        // Arrange
        $migrationDir = '/Users/jot/Projects/Jot/libs/hf-elastic/tests/Examples/migrations-test/elasticsearch';
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
     * @covers \Jot\HfElastic\Command\AbstractCommand::getMigrationFiles
     * @group unit
     * Test that getMigrationFiles correctly filters files by filename
     * What is being tested:
     * - The getMigrationFiles method of the AbstractCommand class with filename filter
     * Conditions/Scenarios:
     * - Filtering migration files by a specific filename
     * - Filtering migration files by a non-existent filename
     * Expected results:
     * - When filtering by a specific filename, only that file should be returned
     * - When filtering by a non-existent filename, no files should be returned
     * @return void
     */
    public function testGetMigrationFilesFiltersFilesByFilename(): void
    {
        // Arrange
        $migrationDir = '/Users/jot/Projects/Jot/libs/hf-elastic/tests/Examples/migrations-test/elasticsearch';
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
