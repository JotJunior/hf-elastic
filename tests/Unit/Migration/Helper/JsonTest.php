<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\Helper;

use Jot\HfElastic\Migration\ElasticType\NestedType;
use Jot\HfElastic\Migration\ElasticType\ObjectType;
use Jot\HfElastic\Migration\Helper\Json;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\Helper\Json
 * @group unit
 */
class JsonTest extends TestCase
{
    private string $tempFile;
    private Json $sut;
    
    protected function setUp(): void
    {
        parent::setUp();
        
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
            'ip_address' => '192.168.1.1',
            'updated_at' => '2025-03-06T16:00:00Z',
            '@version' => 1,
            '@timestamp' => '2025-03-06T16:00:00Z'
        ];
        
        file_put_contents($this->tempFile, json_encode($jsonData));
        $this->sut = new Json($this->tempFile);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Clean up the temporary file
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::__construct
     * @group unit
     * Test that constructor initializes the object correctly with valid JSON
     */
    public function testConstructorWithValidJson(): void
    {
        // Act
        $json = new Json($this->tempFile);
        
        // Assert
        $this->assertInstanceOf(Json::class, $json);
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::__construct
     * @group unit
     * Test that constructor throws exception when file doesn't exist
     */
    public function testConstructorWithInvalidFile(): void
    {
        // Arrange
        $nonExistentFile = '/non/existent/file.json';
        
        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("'$nonExistentFile' is not a valid file or url.");
        
        // Act
        new Json($nonExistentFile);
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::__construct
     * @group unit
     * Test that constructor throws exception when JSON is invalid
     */
    public function testConstructorWithInvalidJson(): void
    {
        // Arrange
        $invalidJsonFile = sys_get_temp_dir() . '/invalid_json.json';
        file_put_contents($invalidJsonFile, '{"invalid": "json"');
        
        try {
            // Assert
            $this->expectException(\Exception::class);
            
            // Act
            new Json($invalidJsonFile);
        } finally {
            if (file_exists($invalidJsonFile)) {
                unlink($invalidJsonFile);
            }
        }
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::body
     * @group unit
     * Test that body method generates correct mapping code
     */
    public function testBody(): void
    {
        // Act
        $body = $this->sut->body();
        
        // Assert
        $this->assertIsString($body);
        
        // Verify field mappings
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'description\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'price\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'quantity\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'is_active\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'tags\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'ip_address\'\);/', $body);
        
        // Verify nested objects
        $this->assertMatchesRegularExpression('/\$metadata = new ObjectType\(\'metadata\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$metadata->\w+\(\'category\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$metadata->\w+\(\'created_at\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$index->object\(\$metadata\);/', $body);
        
        // Verify nested arrays
        $this->assertMatchesRegularExpression('/\$comments = new NestedType\(\'comments\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$comments->\w+\(\'author\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$comments->\w+\(\'content\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$comments->\w+\(\'rating\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$index->nested\(\$comments\);/', $body);
        
        // Verify protected fields are excluded
        $this->assertStringNotContainsString('updated_at', $body);
        $this->assertStringNotContainsString('@version', $body);
        $this->assertStringNotContainsString('@timestamp', $body);
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::body
     * @group unit
     * Test that body method works with custom variable name and data
     */
    public function testBodyWithCustomVarAndData(): void
    {
        // Arrange
        $customData = [
            'custom_field' => 'value',
            'custom_number' => 123
        ];
        
        // Act
        $body = $this->sut->body('custom', $customData);
        
        // Assert
        $this->assertIsString($body);
        $this->assertMatchesRegularExpression('/\$custom->\w+\(\'custom_field\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$custom->\w+\(\'custom_number\'\);/', $body);
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::__toString
     * @group unit
     * Test that __toString method returns the body content
     */
    public function testToString(): void
    {
        // Act
        $string = (string) $this->sut;
        
        // Assert
        $this->assertIsString($string);
        $this->assertStringContainsString('description', $string);
        $this->assertStringContainsString('price', $string);
        $this->assertStringContainsString('quantity', $string);
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::inferElasticType
     * @group unit
     * @dataProvider provideTypesForInference
     * Test that inferElasticType correctly identifies Elasticsearch types
     */
    public function testInferElasticType(mixed $value, string $expectedType): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(Json::class);
        $method = $reflectionClass->getMethod('inferElasticType');
        $method->setAccessible(true);
        
        // Act
        $result = $method->invoke($this->sut, $value);
        
        // Assert
        $this->assertEquals($expectedType, $result);
    }
    
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function provideTypesForInference(): array
    {
        return [
            'date string' => [
                'value' => '2025-03-06T16:00:00Z',
                'expectedType' => 'date'
            ],
            'url string' => [
                'value' => 'https://example.com',
                'expectedType' => 'keyword'
            ],
            'ip address' => [
                'value' => '192.168.1.1',
                'expectedType' => 'ip'
            ],
            'long text' => [
                'value' => str_repeat('a', 201),
                'expectedType' => 'text'
            ],
            'short text' => [
                'value' => 'short text',
                'expectedType' => 'keyword'
            ],
            'integer' => [
                'value' => 10,
                'expectedType' => 'long'
            ],
            'float' => [
                'value' => 99.99,
                'expectedType' => 'double'
            ],
            'boolean' => [
                'value' => true,
                'expectedType' => 'boolean'
            ],
            'nested array' => [
                'value' => [['key' => 'value']],
                'expectedType' => 'nested'
            ],
            'simple array' => [
                'value' => ['value1', 'value2'],
                'expectedType' => 'keyword'
            ],
            'object array' => [
                'value' => ['key' => 'value'],
                'expectedType' => 'object'
            ],
            'null value' => [
                'value' => null,
                'expectedType' => 'keyword'
            ]
        ];
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::getProperties
     * @group unit
     * Test that getProperties correctly extracts properties from nested arrays
     */
    public function testGetProperties(): void
    {
        // Arrange
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
        
        // Act
        $result = $method->invoke($this->sut, $nestedArray);
        
        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('author', $result);
        $this->assertArrayHasKey('content', $result);
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::getProperties
     * @group unit
     * Test that getProperties correctly handles deeply nested structures
     */
    public function testGetPropertiesWithDeepNesting(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(Json::class);
        $method = $reflectionClass->getMethod('getProperties');
        $method->setAccessible(true);
        
        $deeplyNestedArray = [
            [
                'author' => 'User 1',
                'details' => [
                    'location' => 'New York',
                    'preferences' => [
                        'theme' => 'dark',
                        'notifications' => true
                    ]
                ]
            ],
            [
                'author' => 'User 2',
                'details' => [
                    'location' => 'San Francisco',
                    'preferences' => [
                        'theme' => 'light',
                        'notifications' => false
                    ]
                ]
            ]
        ];
        
        // Act
        $result = $method->invoke($this->sut, $deeplyNestedArray);
        
        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('author', $result);
        $this->assertArrayHasKey('details', $result);
        $this->assertIsArray($result['details']);
        $this->assertArrayHasKey('location', $result['details']);
        $this->assertArrayHasKey('preferences', $result['details']);
        $this->assertIsArray($result['details']['preferences']);
        $this->assertArrayHasKey('theme', $result['details']['preferences']);
        $this->assertArrayHasKey('notifications', $result['details']['preferences']);
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::getProperties
     * @group unit
     * Test that getProperties correctly handles non-indexed arrays
     */
    public function testGetPropertiesWithNonIndexedArray(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(Json::class);
        $method = $reflectionClass->getMethod('getProperties');
        $method->setAccessible(true);
        
        $nonIndexedArray = [
            'user' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'address' => [
                    'city' => 'New York',
                    'country' => 'USA'
                ]
            ]
        ];
        
        // Act
        $result = $method->invoke($this->sut, $nonIndexedArray);
        
        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertIsArray($result['user']);
        $this->assertArrayHasKey('name', $result['user']);
        $this->assertArrayHasKey('email', $result['user']);
        $this->assertArrayHasKey('address', $result['user']);
        $this->assertIsArray($result['user']['address']);
        $this->assertArrayHasKey('city', $result['user']['address']);
        $this->assertArrayHasKey('country', $result['user']['address']);
    }
}
