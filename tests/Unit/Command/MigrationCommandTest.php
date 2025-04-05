<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\Unit\Command;

use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\Command\MigrationCommand;
use Jot\HfElastic\Services\FileGenerator;
use Jot\HfElastic\Services\TemplateGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 * @coversNothing
 */
class MigrationCommandTest extends TestCase
{
    private MigrationCommand $sut;

    private ContainerInterface|MockObject $container;

    private ConfigInterface|MockObject $config;

    private MockObject|TemplateGenerator $templateGenerator;

    private FileGenerator|MockObject $fileGenerator;

    private InputInterface|MockObject $input;

    private MockObject|OutputInterface $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->config = $this->createMock(ConfigInterface::class);
        $this->templateGenerator = $this->createMock(TemplateGenerator::class);
        $this->fileGenerator = $this->createMock(FileGenerator::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);

        $this->container->method('get')
            ->willReturnMap([
                [ConfigInterface::class, $this->config],
                [TemplateGenerator::class, $this->templateGenerator],
                [FileGenerator::class, $this->fileGenerator],
            ]);

        $this->sut = new MigrationCommand($this->container, $this->templateGenerator, $this->fileGenerator);
        $this->sut->setInput($this->input);
        $this->sut->setOutput($this->output);
    }

    public function testConfigureSetsCorrectOptions(): void
    {
        // This is implicitly tested in the constructor, but we'll add an explicit test
        // Create a new instance with mocked dependencies to verify configure is called
        $command = new MigrationCommand($this->container, $this->templateGenerator, $this->fileGenerator);

        // Use reflection to access private properties
        $reflection = new ReflectionClass($command);
        $property = $reflection->getProperty('name');
        $property->setAccessible(true);

        // Assert
        $this->assertEquals('elastic:migration', $property->getValue($command));
    }

    public function testHandleCreatesNewMigration(): void
    {
        // Arrange
        $indexName = 'test_index';
        $templateContent = '<?php // Migration template';
        $migrationFile = '/path/to/migrations/elasticsearch/20210101000000-create-test_index.php';

        // Mock createMigrationDirectoryIfNotExists method
        $mockSut = $this->getMockBuilder(MigrationCommand::class)
            ->setConstructorArgs([$this->container, $this->templateGenerator, $this->fileGenerator])
            ->onlyMethods(['createMigrationDirectoryIfNotExists', 'generateMigrationFilename', 'line'])
            ->getMock();

        $mockSut->method('createMigrationDirectoryIfNotExists')
            ->willReturn(true);

        $mockSut->method('generateMigrationFilename')
            ->with($indexName, false)
            ->willReturn($migrationFile);

        $mockSut->expects($this->once())
            ->method('line')
            ->with($this->stringContains('Run'));

        $mockSut->setInput($this->input);
        $mockSut->setOutput($this->output);

        // Input expectations
        $this->input->method('getArgument')
            ->with('index')
            ->willReturn($indexName);

        $this->input->method('getOption')
            ->willReturnMap([
                ['json-schema', ''],
                ['json', ''],
                ['update', false],
                ['force', false],
            ]);

        // Template generator expectations
        $this->templateGenerator->expects($this->once())
            ->method('generateCreateTemplate')
            ->with($indexName, '', '')
            ->willReturn($templateContent);

        // File generator expectations
        $this->fileGenerator->expects($this->once())
            ->method('generateFile')
            ->with($migrationFile, $templateContent, $mockSut, false);

        // Act
        $result = $mockSut->handle();

        // Assert
        $this->assertEquals(0, $result);
    }

    public function testHandleCreatesUpdateMigration(): void
    {
        // Arrange
        $indexName = 'test_index';
        $templateContent = '<?php // Update migration template';
        $migrationFile = '/path/to/migrations/elasticsearch/20210101000000-update-test_index.php';

        // Mock createMigrationDirectoryIfNotExists method
        $mockSut = $this->getMockBuilder(MigrationCommand::class)
            ->setConstructorArgs([$this->container, $this->templateGenerator, $this->fileGenerator])
            ->onlyMethods(['createMigrationDirectoryIfNotExists', 'generateMigrationFilename', 'line'])
            ->getMock();

        $mockSut->method('createMigrationDirectoryIfNotExists')
            ->willReturn(true);

        $mockSut->method('generateMigrationFilename')
            ->with($indexName, true)
            ->willReturn($migrationFile);

        $mockSut->expects($this->once())
            ->method('line')
            ->with($this->stringContains('Run'));

        $mockSut->setInput($this->input);
        $mockSut->setOutput($this->output);

        // Input expectations
        $this->input->method('getArgument')
            ->with('index')
            ->willReturn($indexName);

        $this->input->method('getOption')
            ->willReturnMap([
                ['json-schema', ''],
                ['json', ''],
                ['update', true],
                ['force', false],
            ]);

        // Template generator expectations
        $this->templateGenerator->expects($this->once())
            ->method('generateUpdateTemplate')
            ->with($indexName)
            ->willReturn($templateContent);

        // File generator expectations
        $this->fileGenerator->expects($this->once())
            ->method('generateFile')
            ->with($migrationFile, $templateContent, $mockSut, false);

        // Act
        $result = $mockSut->handle();

        // Assert
        $this->assertEquals(0, $result);
    }

    public function testHandleFailsWhenBothJsonOptionsProvided(): void
    {
        // Arrange
        $indexName = 'test_index';

        // Mock createMigrationDirectoryIfNotExists method
        $mockSut = $this->getMockBuilder(MigrationCommand::class)
            ->setConstructorArgs([$this->container, $this->templateGenerator, $this->fileGenerator])
            ->onlyMethods(['createMigrationDirectoryIfNotExists', 'line'])
            ->getMock();

        $mockSut->method('createMigrationDirectoryIfNotExists')
            ->willReturn(true);

        $mockSut->expects($this->once())
            ->method('line')
            ->with($this->stringContains('[ERROR]'));

        $mockSut->setInput($this->input);
        $mockSut->setOutput($this->output);

        // Input expectations
        $this->input->method('getArgument')
            ->with('index')
            ->willReturn($indexName);

        $this->input->method('getOption')
            ->willReturnMap([
                ['json-schema', 'schema.json'],
                ['json', 'data.json'],
                ['update', false],
                ['force', false],
            ]);

        // Act
        $result = $mockSut->handle();

        // Assert
        $this->assertEquals(1, $result);
    }

    public function testHandleFailsWhenMigrationDirectoryCannotBeCreated(): void
    {
        // Arrange
        $indexName = 'test_index';

        // Mock createMigrationDirectoryIfNotExists method
        $mockSut = $this->getMockBuilder(MigrationCommand::class)
            ->setConstructorArgs([$this->container, $this->templateGenerator, $this->fileGenerator])
            ->onlyMethods(['createMigrationDirectoryIfNotExists', 'line'])
            ->getMock();

        $mockSut->method('createMigrationDirectoryIfNotExists')
            ->willReturn(false);

        $mockSut->expects($this->once())
            ->method('line')
            ->with($this->stringContains('[ERROR]'));

        $mockSut->setInput($this->input);
        $mockSut->setOutput($this->output);

        // Act
        $result = $mockSut->handle();

        // Assert
        $this->assertEquals(1, $result);
    }

    public function testGenerateMigrationFilename(): void
    {
        // Arrange
        $indexName = 'test_index';
        $update = false;

        // Set the migrationDirectory property using reflection
        $reflection = new ReflectionClass(MigrationCommand::class);
        $property = $reflection->getProperty('migrationDirectory');
        $property->setAccessible(true);
        $property->setValue($this->sut, '/path/to/migrations/elasticsearch');

        // Use reflection to access protected method
        $method = $reflection->getMethod('generateMigrationFilename');
        $method->setAccessible(true);

        // Como a data é apenas para ordenação, não precisamos testar o valor exato
        // mas apenas o formato e a estrutura do nome do arquivo

        // Act
        $result = $method->invoke($this->sut, $indexName, $update);

        // Assert
        // Verificar se o caminho base está correto
        $this->assertStringStartsWith('/path/to/migrations/elasticsearch/', $result);

        // Verificar se o formato do nome do arquivo está correto
        $filename = basename($result);
        if ($update) {
            $this->assertMatchesRegularExpression('/^\d{14}-update-test_index\.php$/', $filename);
        } else {
            $this->assertMatchesRegularExpression('/^\d{14}-create-test_index\.php$/', $filename);
        }

        // Testar com update = true
        $resultUpdate = $method->invoke($this->sut, $indexName, true);
        $filenameUpdate = basename($resultUpdate);
        $this->assertMatchesRegularExpression('/^\d{14}-update-test_index\.php$/', $filenameUpdate);
    }
}
