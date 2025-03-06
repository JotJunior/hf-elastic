<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Services;

use Hyperf\Command\Command;
use Jot\HfElastic\Services\FileGenerator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * @covers \Jot\HfElastic\Services\FileGenerator
 */
class FileGeneratorTest extends TestCase
{
    private FileGenerator $sut;
    private vfsStreamDirectory $root;
    private Command|MockObject $command;

    protected function setUp(): void
    {        
        parent::setUp();
        $this->root = vfsStream::setup('home');
        $this->command = $this->createMock(Command::class);
        $this->sut = new FileGenerator();
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\FileGenerator::generateFile
     */
    public function testGenerateFileCreatesNewFile(): void
    {
        // Arrange
        $filePath = vfsStream::url('home/test.php');
        $contents = '<?php echo "Hello World";';
        
        $this->command->expects($this->once())
            ->method('line')
            ->with($this->stringContains('[OK]'));

        // Act
        $this->sut->generateFile($filePath, $contents, $this->command);

        // Assert
        $this->assertTrue($this->root->hasChild('test.php'));
        $this->assertEquals($contents, file_get_contents($filePath));
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\FileGenerator::generateFile
     */
    public function testGenerateFileOverwritesExistingFileWhenForced(): void
    {
        // Arrange
        $filePath = vfsStream::url('home/test.php');
        $initialContents = '<?php echo "Initial content";';
        $newContents = '<?php echo "New content";';
        
        // Create the file initially
        file_put_contents($filePath, $initialContents);
        
        $this->command->expects($this->once())
            ->method('line')
            ->with($this->stringContains('[OK]'));

        // Act
        $this->sut->generateFile($filePath, $newContents, $this->command, true);

        // Assert
        $this->assertTrue($this->root->hasChild('test.php'));
        $this->assertEquals($newContents, file_get_contents($filePath));
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\FileGenerator::generateFile
     */
    public function testGenerateFileAsksForConfirmationWhenFileExistsAndNotForced(): void
    {
        // Arrange
        $filePath = vfsStream::url('home/test.php');
        $initialContents = '<?php echo "Initial content";';
        $newContents = '<?php echo "New content";';
        
        // Create the file initially
        file_put_contents($filePath, $initialContents);
        
        $this->command->expects($this->once())
            ->method('ask')
            ->with($this->stringContains('already exists'), 'n')
            ->willReturn('y');
            
        $this->command->expects($this->once())
            ->method('line')
            ->with($this->stringContains('[OK]'));

        // Act
        $this->sut->generateFile($filePath, $newContents, $this->command, false);

        // Assert
        $this->assertTrue($this->root->hasChild('test.php'));
        $this->assertEquals($newContents, file_get_contents($filePath));
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\FileGenerator::generateFile
     */
    public function testGenerateFileSkipsWhenUserDoesNotConfirm(): void
    {
        // Arrange
        $filePath = vfsStream::url('home/test.php');
        $initialContents = '<?php echo "Initial content";';
        $newContents = '<?php echo "New content";';
        
        // Create the file initially
        file_put_contents($filePath, $initialContents);
        
        $this->command->expects($this->once())
            ->method('ask')
            ->with($this->stringContains('already exists'), 'n')
            ->willReturn('n');
            
        $this->command->expects($this->once())
            ->method('line')
            ->with($this->stringContains('[SKIP]'));

        // Act
        $this->sut->generateFile($filePath, $newContents, $this->command, false);

        // Assert
        $this->assertTrue($this->root->hasChild('test.php'));
        $this->assertEquals($initialContents, file_get_contents($filePath));
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\FileGenerator::generateFile
     */
    public function testGenerateFileSetsForceFlagWhenUserAnswersA(): void
    {
        // Arrange
        $filePath = vfsStream::url('home/test.php');
        $initialContents = '<?php echo "Initial content";';
        $newContents = '<?php echo "New content";';
        
        // Create the file initially
        file_put_contents($filePath, $initialContents);
        
        $this->command->expects($this->once())
            ->method('ask')
            ->with($this->stringContains('already exists'), 'n')
            ->willReturn('a');
            
        $this->command->expects($this->once())
            ->method('line')
            ->with($this->stringContains('[OK]'));

        // Act
        // File should be overwritten when user answers 'a'
        $this->sut->generateFile($filePath, $newContents, $this->command, false);

        // Assert
        $this->assertTrue($this->root->hasChild('test.php'));
        $this->assertEquals($newContents, file_get_contents($filePath));
    }
}
