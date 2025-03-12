<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Command;

use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\Command\DestroyCommand;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DestroyCommandTest extends TestCase
{
    private DestroyCommand $sut;
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
        
        $this->sut = new DestroyCommand($this->container);
        $this->sut->setInput($this->input);
        $this->sut->setOutput($this->output);
    }

    public function testConfigureSetsCorrectOptions(): void
    {
        // Create a new instance with mocked dependencies to verify configure is called
        $command = new DestroyCommand($this->container);
        
        // Use reflection to access private properties
        $reflection = new \ReflectionClass($command);
        $property = $reflection->getProperty('name');
        $property->setAccessible(true);
        
        // Assert
        $this->assertEquals('elastic:destroy', $property->getValue($command));
    }

    public function testHandleAbortsWhenUserDoesNotConfirm(): void
    {
        // Arrange
        $mockSut = $this->getMockBuilder(DestroyCommand::class)
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

    public function testHandleReturnsErrorWhenMigrationDirectoryDoesNotExist(): void
    {
        // Arrange
        $mockSut = $this->getMockBuilder(DestroyCommand::class)
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

    public function testHandleReturnsSuccessWhenNoMigrationsFound(): void
    {
        // Arrange
        $mockSut = $this->getMockBuilder(DestroyCommand::class)
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

    public function testHandleDestroysIndices(): void
    {
        // Arrange
        // Create mock migration objects
        $migration1 = new class {
            const INDEX_NAME = 'test_index_1';
            public function delete($indexName) {}
        };
        
        $migration2 = new class {
            const INDEX_NAME = 'test_index_2';
            public function delete($indexName) {}
        };
        
        $migrations = [
            '/path/to/migrations/elasticsearch/migration1.php' => $migration1,
            '/path/to/migrations/elasticsearch/migration2.php' => $migration2
        ];
        
        $mockSut = $this->getMockBuilder(DestroyCommand::class)
            ->setConstructorArgs([$this->container])
            ->onlyMethods(['ask', 'line', 'newLine', 'migrationDirectoryExists', 'getMigrationFiles'])
            ->getMock();
            
        $mockSut->method('ask')
            ->willReturn('y');
            
        $mockSut->method('migrationDirectoryExists')
            ->willReturn(true);
            
        $mockSut->method('getMigrationFiles')
            ->willReturn($migrations);
            
        $mockSut->expects($this->exactly(4))
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

    public function testHandleHandlesExceptionsGracefully(): void
    {
        // Arrange
        // Create mock migration objects
        $migration1 = new class {
            const INDEX_NAME = 'test_index_1';
            public function delete($indexName) { throw new \Exception('Test exception'); }
        };
        
        $migrations = [
            '/path/to/migrations/elasticsearch/migration1.php' => $migration1
        ];
        
        $mockSut = $this->getMockBuilder(DestroyCommand::class)
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
