<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\Helper;

use Jot\HfElastic\Exception\InvalidFileException;
use Jot\HfElastic\Exception\InvalidJsonTemplateException;
use Jot\HfElastic\Exception\UnreadableFileException;
use Jot\HfElastic\Migration\ElasticType\NestedType;
use Jot\HfElastic\Migration\ElasticType\ObjectType;
use Jot\HfElastic\Migration\Helper\Json;
use JsonException;
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
     * Test that constructor throws InvalidFileException when file doesn't exist
     * 
     * What is being tested:
     * - The behavior when a non-existent file is provided to the constructor
     * 
     * Conditions/Scenarios:
     * - A file path that does not exist in the filesystem
     * 
     * Expected results:
     * - An InvalidFileException should be thrown with appropriate message
     */
    public function testConstructorWithInvalidFile(): void
    {
        // Arrange
        $nonExistentFile = '/non/existent/file.json';
        
        // Assert
        $this->expectException(InvalidFileException::class);
        $this->expectExceptionMessage(sprintf('%s is not a valid file or url.', $nonExistentFile));
        
        // Act
        new Json($nonExistentFile);
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::__construct
     * @group unit
     * Test that constructor throws UnreadableFileException when JSON is invalid
     * 
     * What is being tested:
     * - The behavior when an invalid JSON file is provided to the constructor
     * 
     * Conditions/Scenarios:
     * - A file with malformed JSON content
     * 
     * Expected results:
     * - An UnreadableFileException should be thrown with appropriate message
     */
    public function testConstructorWithInvalidJson(): void
    {
        // Arrange
        $invalidJsonFile = sys_get_temp_dir() . '/invalid_json.json';
        file_put_contents($invalidJsonFile, '{"invalid": "json"');
        
        try {
            // Assert
            $this->expectException(UnreadableFileException::class);
            
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
     * @covers \Jot\HfElastic\Migration\Helper\Json::__construct
     * @group unit
     * Test that constructor throws UnreadableFileException when file cannot be read
     * 
     * What is being tested:
     * - The behavior when a file cannot be read
     * 
     * Conditions/Scenarios:
     * - A file that exists but cannot be read due to permissions or other issues
     * 
     * Expected results:
     * - An UnreadableFileException should be thrown with appropriate message
     * 
     * @skip Não é possível mockar funções globais com PHPUnit padrão
     */
    public function testConstructorWithUnreadableFile(): void
    {
        $this->markTestSkipped('Este teste requer uma biblioteca adicional para mockar funções globais.');
        
        // Arrange
        $unreadableFile = sys_get_temp_dir() . '/unreadable_file.json';
        file_put_contents($unreadableFile, '{}');
        
        try {
            // Não podemos mockar file_get_contents com PHPUnit padrão
            // Precisaríamos de uma biblioteca como php-mock ou similar
            
            // Assert
            $this->expectException(UnreadableFileException::class);
            
            // Act
            new Json($unreadableFile);
        } finally {
            if (file_exists($unreadableFile)) {
                unlink($unreadableFile);
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
            ]
        ];
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::inferElasticType
     * @group unit
     * Test that inferElasticType throws InvalidJsonTemplateException for null values
     * 
     * What is being tested:
     * - The behavior when a null value is provided to inferElasticType
     * 
     * Conditions/Scenarios:
     * - A null value is passed to the method
     * 
     * Expected results:
     * - An InvalidJsonTemplateException should be thrown
     */
    public function testInferElasticTypeWithNullValue(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(Json::class);
        $method = $reflectionClass->getMethod('inferElasticType');
        $method->setAccessible(true);
        
        // Assert
        $this->expectException(InvalidJsonTemplateException::class);
        
        // Act
        $method->invoke($this->sut, null);
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
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::getProperties
     * @group unit
     * Test that getProperties returns an empty array for empty input
     * 
     * What is being tested:
     * - The behavior when an empty array is provided to getProperties
     * 
     * Conditions/Scenarios:
     * - An empty array is passed to the method
     * 
     * Expected results:
     * - An empty array should be returned
     */
    public function testGetPropertiesWithEmptyArray(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(Json::class);
        $method = $reflectionClass->getMethod('getProperties');
        $method->setAccessible(true);
        
        // Act
        $result = $method->invoke($this->sut, []);
        
        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::processValue
     * @group unit
     * Test that processValue correctly processes scalar values
     * 
     * What is being tested:
     * - The behavior of processValue with scalar values
     * 
     * Conditions/Scenarios:
     * - A scalar value is passed to the method
     * 
     * Expected results:
     * - The scalar value should be returned unchanged
     */
    public function testProcessValueWithScalarValue(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(Json::class);
        $method = $reflectionClass->getMethod('processValue');
        $method->setAccessible(true);
        
        // Act
        $result = $method->invoke($this->sut, 'key', 'value', []);
        
        // Assert
        $this->assertEquals('value', $result);
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\Helper\Json::processValue
     * @group unit
     * Test that processValue correctly processes array values
     * 
     * What is being tested:
     * - The behavior of processValue with array values
     * 
     * Conditions/Scenarios:
     * - An array value is passed to the method
     * 
     * Expected results:
     * - The array should be processed by getProperties and merged with existing values
     */
    public function testProcessValueWithArrayValue(): void
    {
        // Arrange
        $reflectionClass = new \ReflectionClass(Json::class);
        $method = $reflectionClass->getMethod('processValue');
        $method->setAccessible(true);
        
        $existingData = ['key' => ['subKey1' => 'value1']];
        $newData = ['subKey2' => 'value2'];
        
        // Act
        $result = $method->invoke($this->sut, 'key', $newData, $existingData);
        
        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('subKey1', $result);
        $this->assertEquals('value1', $result['subKey1']);
        $this->assertArrayHasKey('subKey2', $result);
        $this->assertEquals('value2', $result['subKey2']);
    }
}
