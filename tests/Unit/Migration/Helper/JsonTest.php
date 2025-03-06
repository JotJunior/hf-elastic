<?php

namespace Jot\HfElastic\Tests\Unit\Migration\Helper;

use Jot\HfElastic\Migration\Helper\Json;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    private string $tempFile;
    
    protected function setUp(): void
    {
        // Create a temporary JSON file for testing
        $this->tempFile = sys_get_temp_dir() . '/test_json_' . uniqid() . '.json';
        $jsonData = [
            'title' => 'Test Title',
            'description' => 'This is a long text description that should be mapped to text type',
            'price' => 99.99,
            'quantity' => 10,
            'is_active' => true,
            'tags' => ['tag1', 'tag2', 'tag3'],
            'metadata' => [
                'category' => 'Test Category',
                'created_at' => '2025-03-06T16:00:00Z'
            ],
            'comments' => [
                [
                    'author' => 'User 1',
                    'content' => 'Comment 1',
                    'rating' => 5
                ],
                [
                    'author' => 'User 2',
                    'content' => 'Comment 2',
                    'rating' => 4
                ]
            ],
            'ip_address' => '192.168.1.1'
        ];
        
        file_put_contents($this->tempFile, json_encode($jsonData));
    }
    
    protected function tearDown(): void
    {
        // Clean up the temporary file
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }
    
    public function testConstructorWithValidJson(): void
    {
        $json = new Json($this->tempFile);
        $this->assertInstanceOf(Json::class, $json);
    }
    
    public function testConstructorWithInvalidFile(): void
    {
        $this->expectException(\Exception::class);
        new Json('/non/existent/file.json');
    }
    
    public function testConstructorWithInvalidJson(): void
    {
        $invalidJsonFile = sys_get_temp_dir() . '/invalid_json.json';
        file_put_contents($invalidJsonFile, '{"invalid": "json"');
        
        try {
            $this->expectException(\Exception::class);
            new Json($invalidJsonFile);
        } finally {
            unlink($invalidJsonFile);
        }
    }
    
    public function testBody(): void
    {
        $json = new Json($this->tempFile);
        $body = $json->body();
        
        // Check that the body contains the expected mapping code
        $this->assertIsString($body);
        // Verificando se o corpo contém os campos esperados, independente do tipo
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'description\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'price\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'quantity\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'is_active\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'tags\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'ip_address\'\);/', $body);
        
        // Check for nested objects
        $this->assertMatchesRegularExpression('/\$metadata = new ObjectType\(\'metadata\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$metadata->\w+\(\'category\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$metadata->\w+\(\'created_at\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$index->object\(\$metadata\);/', $body);
        
        // Check for nested arrays
        $this->assertMatchesRegularExpression('/\$comments = new NestedType\(\'comments\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$comments->\w+\(\'author\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$comments->\w+\(\'content\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$comments->\w+\(\'rating\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$index->nested\(\$comments\);/', $body);
    }
    
    public function testToString(): void
    {
        $json = new Json($this->tempFile);
        $string = (string) $json;
        
        $this->assertIsString($string);
        $this->assertStringContainsString('description', $string);
    }
    
    public function testInferElasticType(): void
    {
        $json = new Json($this->tempFile);
        $reflectionClass = new \ReflectionClass(Json::class);
        $method = $reflectionClass->getMethod('inferElasticType');
        $method->setAccessible(true);
        
        // Test string types
        $this->assertEquals('date', $method->invoke($json, '2025-03-06T16:00:00Z'));
        $this->assertEquals('keyword', $method->invoke($json, 'https://example.com'));
        $this->assertEquals('ip', $method->invoke($json, '192.168.1.1'));
        $this->assertEquals('text', $method->invoke($json, str_repeat('a', 201)));
        $this->assertEquals('keyword', $method->invoke($json, 'short text'));
        
        // Test numeric types
        $this->assertEquals('long', $method->invoke($json, 10));
        $this->assertEquals('double', $method->invoke($json, 99.99));
        
        // Test boolean
        $this->assertEquals('boolean', $method->invoke($json, true));
        
        // Test arrays
        $this->assertEquals('nested', $method->invoke($json, [['key' => 'value']]));
        $this->assertEquals('keyword', $method->invoke($json, ['value1', 'value2']));
        $this->assertEquals('object', $method->invoke($json, ['key' => 'value']));
    }
    
    public function testGetProperties(): void
    {
        $json = new Json($this->tempFile);
        $reflectionClass = new \ReflectionClass(Json::class);
        $method = $reflectionClass->getMethod('getProperties');
        $method->setAccessible(true);
        
        $nestedArray = [
            [
                'author' => 'User 1',
                'content' => 'Comment 1'
            ],
            [
                'author' => 'User 2',
                'content' => 'Comment 2'
            ]
        ];
        
        $result = $method->invoke($json, $nestedArray);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('author', $result);
        $this->assertArrayHasKey('content', $result);
    }
}
