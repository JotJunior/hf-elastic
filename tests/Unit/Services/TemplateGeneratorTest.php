<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Services;

use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\Migration\Helper\JsonSchema;
use Jot\HfElastic\Migration\Helper\Json;
use Jot\HfElastic\Services\TemplateGenerator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Jot\HfElastic\Services\TemplateGenerator
 */
class TemplateGeneratorTest extends TestCase
{
    private TemplateGenerator $sut;
    private ConfigInterface|MockObject $config;

    protected function setUp(): void
    {        
        parent::setUp();
        $this->config = $this->createMock(ConfigInterface::class);
        $this->sut = new TemplateGenerator($this->config);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\TemplateGenerator::generateUpdateTemplate
     */
    public function testGenerateUpdateTemplate(): void
    {
        // Arrange
        $indexName = 'test_index';

        // Act
        $result = $this->sut->generateUpdateTemplate($indexName);

        // Assert
        $this->assertIsString($result);
        $this->assertStringContainsString($indexName, $result);
        $this->assertStringContainsString('public function up()', $result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\TemplateGenerator::generateCreateTemplate
     */
    public function testGenerateCreateTemplateWithoutJsonInput(): void
    {
        // Arrange
        $indexName = 'test_index';
        $this->config->method('get')
            ->willReturnMap([
                ['hf_elastic', null, ['dynamic' => 'strict', 'settings' => []]]
            ]);

        // Act
        $result = $this->sut->generateCreateTemplate($indexName, '', '');

        // Assert
        $this->assertIsString($result);
        $this->assertStringContainsString($indexName, $result);
        $this->assertStringContainsString('public function up()', $result);

        $this->assertStringContainsString('strict', $result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\TemplateGenerator::generateCreateTemplate
     */
    public function testGenerateCreateTemplateWithJsonSchema(): void
    {
        // Arrange
        $indexName = 'test_index';
        $jsonSchemaPath = 'path/to/schema.json';
        
        $this->config->method('get')
            ->willReturnMap([
                ['hf_elastic', null, ['dynamic' => 'strict', 'settings' => []]]
            ]);
        
        // Mock the JsonSchema class using a PHP-mock
        $this->markTestSkipped('Need to mock the JsonSchema class constructor');
        
        // Act
        $result = $this->sut->generateCreateTemplate($indexName, $jsonSchemaPath, '');
        
        // Assert
        $this->assertIsString($result);
        $this->assertStringContainsString($indexName, $result);
        $this->assertStringContainsString('public function up()', $result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\TemplateGenerator::generateCreateTemplate
     */
    public function testGenerateCreateTemplateWithJson(): void
    {
        // Arrange
        $indexName = 'test_index';
        $jsonPath = 'path/to/data.json';
        
        $this->config->method('get')
            ->willReturnMap([
                ['hf_elastic', null, ['dynamic' => 'strict', 'settings' => []]]
            ]);
        
        // Mock the Json class using a PHP-mock
        $this->markTestSkipped('Need to mock the Json class constructor');
        
        // Act
        $result = $this->sut->generateCreateTemplate($indexName, '', $jsonPath);
        
        // Assert
        $this->assertIsString($result);
        $this->assertStringContainsString($indexName, $result);
        $this->assertStringContainsString('public function up()', $result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\TemplateGenerator::getDynamic
     */
    public function testGetDynamic(): void
    {
        // Arrange
        $this->config->method('get')
            ->with('hf_elastic')
            ->willReturn(['dynamic' => 'true']);

        // Use reflection to access protected method
        $reflection = new \ReflectionClass(TemplateGenerator::class);
        $method = $reflection->getMethod('getDynamic');
        $method->setAccessible(true);

        // Act
        $result = $method->invoke($this->sut);

        // Assert
        $this->assertEquals('true', $result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\TemplateGenerator::getSettings
     */
    public function testGetSettings(): void
    {
        // Arrange
        $settings = [
            'index' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 1,
            ],
        ];
        
        $this->config->method('get')
            ->with('hf_elastic')
            ->willReturn(['settings' => $settings]);

        // Use reflection to access protected method
        $reflection = new \ReflectionClass(TemplateGenerator::class);
        $method = $reflection->getMethod('getSettings');
        $method->setAccessible(true);

        // Act
        $result = $method->invoke($this->sut);

        // Assert
        $this->assertIsString($result);
        $this->assertStringContainsString('number_of_shards', $result);
        $this->assertStringContainsString('number_of_replicas', $result);
    }

    /**
     * @test
     * @group unit
     * @covers \Jot\HfElastic\Services\TemplateGenerator::parseTemplate
     */
    public function testParseTemplate(): void
    {
        // Arrange
        $templateName = 'migration-create';
        $variables = [
            'index' => 'test_index',
            'dynamic' => 'strict',
            'settings' => '[]',
            'contents' => '',
        ];

        // Use reflection to access protected method
        $reflection = new \ReflectionClass(TemplateGenerator::class);
        $method = $reflection->getMethod('parseTemplate');
        $method->setAccessible(true);

        // Act
        $result = $method->invoke($this->sut, $templateName, $variables);

        // Assert
        $this->assertIsString($result);
        $this->assertStringContainsString('test_index', $result);
    }
}
