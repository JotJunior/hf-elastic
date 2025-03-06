<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Command;

use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\Command\ResetCommand;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @covers \Jot\HfElastic\Command\ResetCommand
 */
class ResetCommandTest extends TestCase
{
    private ResetCommand $sut;
    private ContainerInterface|MockObject $container;
    private ConfigInterface|MockObject $config;
    private InputInterface|MockObject $input;
    private OutputInterface|MockObject $output;

    protected function setUp(): void
    {        
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->config = $this->createMock(ConfigInterface::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        
        $this->container->method('get')
            ->with(ConfigInterface::class)
            ->willReturn($this->config);
        
        $this->sut = new ResetCommand($this->container);
        $this->sut->setInput($this->input);
        $this->sut->setOutput($this->output);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\ResetCommand::configure
     */
    public function testConfigureSetsCorrectOptions(): void
    {
        // Create a new instance with mocked dependencies to verify configure is called
        $command = new ResetCommand($this->container);
        
        // Use reflection to access private properties
        $reflection = new \ReflectionClass($command);
        $property = $reflection->getProperty('name');
        $property->setAccessible(true);
        
        // Assert
        $this->assertEquals('elastic:reset', $property->getValue($command));
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\ResetCommand::handle
     */
    public function testHandleAbortsWhenUserDoesNotConfirm(): void
    {
        // Arrange
        $mockSut = $this->getMockBuilder(ResetCommand::class)
            ->setConstructorArgs([$this->container])
            ->onlyMethods(['ask', 'line', 'newLine'])
            ->getMock();
            
        $mockSut->method('ask')
            ->willReturn('N');
            
        $mockSut->expects($this->exactly(3))
            ->method('line');
            
        $mockSut->expects($this->once())
            ->method('newLine');
        
        $mockSut->setInput($this->input);
        $mockSut->setOutput($this->output);

        // Act
        $result = $mockSut->handle();

        // Assert
        $this->assertEquals(0, $result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\ResetCommand::handle
     */
    public function testHandleReturnsErrorWhenMigrationDirectoryDoesNotExist(): void
    {
        // Arrange
        $mockSut = $this->getMockBuilder(ResetCommand::class)
            ->setConstructorArgs([$this->container])
            ->onlyMethods(['ask', 'line', 'newLine', 'migrationDirectoryExists'])
            ->getMock();
            
        $mockSut->method('ask')
            ->willReturn('y');
            
        $mockSut->method('migrationDirectoryExists')
            ->willReturn(false);
            
        $mockSut->expects($this->exactly(2))
            ->method('line');
            
        $mockSut->expects($this->once())
            ->method('newLine');
        
        $mockSut->setInput($this->input);
        $mockSut->setOutput($this->output);

        // Act
        $result = $mockSut->handle();

        // Assert
        $this->assertEquals(1, $result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\ResetCommand::handle
     */
    public function testHandleReturnsSuccessWhenNoMigrationsFound(): void
    {
        // Arrange
        $mockSut = $this->getMockBuilder(ResetCommand::class)
            ->setConstructorArgs([$this->container])
            ->onlyMethods(['ask', 'line', 'newLine', 'migrationDirectoryExists', 'getMigrationFiles'])
            ->getMock();
            
        $mockSut->method('ask')
            ->willReturn('y');
            
        $mockSut->method('migrationDirectoryExists')
            ->willReturn(true);
            
        $mockSut->method('getMigrationFiles')
            ->willReturn([]);
            
        $mockSut->expects($this->exactly(3))
            ->method('line');
            
        $mockSut->expects($this->once())
            ->method('newLine');
        
        $mockSut->setInput($this->input);
        $mockSut->setOutput($this->output);
        
        // Input expectations
        $this->input->method('getOption')
            ->willReturnMap([
                ['index', null],
                ['file', null]
            ]);

        // Act
        $result = $mockSut->handle();

        // Assert
        $this->assertEquals(0, $result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\ResetCommand::handle
     */
    public function testHandleResetsIndices(): void
    {
        // Arrange
        // Create mock migration objects with addPrefix property
        $migration1 = new class {
            const INDEX_NAME = 'test_index_1';
            public $addPrefix = false;
            public function delete($indexName) {}
            public function up() {}
        };
        
        $migration2 = new class {
            const INDEX_NAME = 'test_index_2';
            public $addPrefix = true;
            public function delete($indexName) {}
            public function up() {}
        };
        
        $migrations = [
            '/path/to/migrations/elasticsearch/migration1.php' => $migration1,
            '/path/to/migrations/elasticsearch/migration2.php' => $migration2
        ];
        
        $mockSut = $this->getMockBuilder(ResetCommand::class)
            ->setConstructorArgs([$this->container])
            ->onlyMethods(['ask', 'line', 'newLine', 'migrationDirectoryExists', 'getMigrationFiles'])
            ->getMock();
            
        $mockSut->method('ask')
            ->willReturn('y');
            
        $mockSut->method('migrationDirectoryExists')
            ->willReturn(true);
            
        $mockSut->method('getMigrationFiles')
            ->willReturn($migrations);
            
        $mockSut->expects($this->exactly(6))
            ->method('line');
            
        $mockSut->expects($this->once())
            ->method('newLine');
        
        $mockSut->setInput($this->input);
        $mockSut->setOutput($this->output);
        
        // Config expectations for prefix
        $this->config->method('get')
            ->with('hf_elastic.prefix')
            ->willReturn('prefix');
        
        // Input expectations
        $this->input->method('getOption')
            ->willReturnMap([
                ['index', null],
                ['file', null]
            ]);

        // Act
        $result = $mockSut->handle();

        // Assert
        $this->assertEquals(0, $result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Command\ResetCommand::handle
     */
    public function testHandleHandlesExceptionsGracefully(): void
    {
        // Arrange
        // Create mock migration objects
        $migration1 = new class {
            const INDEX_NAME = 'test_index_1';
            public $addPrefix = false;
            public function delete($indexName) { throw new \Exception('Test exception'); }
            public function up() {}
        };
        
        $migrations = [
            '/path/to/migrations/elasticsearch/migration1.php' => $migration1
        ];
        
        $mockSut = $this->getMockBuilder(ResetCommand::class)
            ->setConstructorArgs([$this->container])
            ->onlyMethods(['ask', 'line', 'newLine', 'migrationDirectoryExists', 'getMigrationFiles'])
            ->getMock();
            
        $mockSut->method('ask')
            ->willReturn('y');
            
        $mockSut->method('migrationDirectoryExists')
            ->willReturn(true);
            
        $mockSut->method('getMigrationFiles')
            ->willReturn($migrations);
            
        $mockSut->expects($this->exactly(3))
            ->method('line');
            
        $mockSut->expects($this->once())
            ->method('newLine');
        
        $mockSut->setInput($this->input);
        $mockSut->setOutput($this->output);
        
        // Input expectations
        $this->input->method('getOption')
            ->willReturnMap([
                ['index', null],
                ['file', null]
            ]);

        // Act
        $result = $mockSut->handle();

        // Assert
        $this->assertEquals(0, $result);
    }
}
