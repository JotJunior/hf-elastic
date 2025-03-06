<?php

namespace Jot\HfElastic\Tests\Unit\Migration\Helper;

use Jot\HfElastic\Migration\Helper\JsonSchema;
use PHPUnit\Framework\TestCase;

class JsonSchemaTest extends TestCase
{
    private string $tempFile;
    
    protected function setUp(): void
    {
        // Create a temporary JSON Schema file for testing
        $this->tempFile = sys_get_temp_dir() . '/test_schema_' . uniqid() . '.json';
        $schemaData = [
            'type' => 'object',
            'properties' => [
                'title' => [
                    'type' => 'string',
                    'maxLength' => 100
                ],
                'description' => [
                    'type' => 'string'
                ],
                'price' => [
                    'type' => 'number',
                    'minimum' => 0
                ],
                'quantity' => [
                    'type' => 'integer',
                    'minimum' => 0
                ],
                'is_active' => [
                    'type' => 'boolean'
                ],
                'tags' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string'
                    ]
                ],
                'metadata' => [
                    'type' => 'object',
                    'properties' => [
                        'category' => [
                            'type' => 'string'
                        ],
                        'created_at' => [
                            'type' => 'string',
                            'format' => 'date-time'
                        ]
                    ]
                ],
                'comments' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'author' => [
                                'type' => 'string'
                            ],
                            'content' => [
                                'type' => 'string'
                            ],
                            'rating' => [
                                'type' => 'integer',
                                'minimum' => 1,
                                'maximum' => 5
                            ]
                        ]
                    ]
                ]
            ],
            'required' => ['title', 'price']
        ];
        
        file_put_contents($this->tempFile, json_encode($schemaData));
    }
    
    protected function tearDown(): void
    {
        // Clean up the temporary file
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }
    
    public function testConstructorWithValidJsonSchema(): void
    {
        $jsonSchema = new JsonSchema($this->tempFile);
        $this->assertInstanceOf(JsonSchema::class, $jsonSchema);
    }
    
    public function testConstructorWithInvalidFile(): void
    {
        $this->expectException(\Exception::class);
        new JsonSchema('/non/existent/file.json');
    }
    
    public function testConstructorWithInvalidJson(): void
    {
        $invalidJsonFile = sys_get_temp_dir() . '/invalid_schema.json';
        file_put_contents($invalidJsonFile, '{"invalid": "json"');
        
        try {
            $this->expectException(\Exception::class);
            new JsonSchema($invalidJsonFile);
        } finally {
            unlink($invalidJsonFile);
        }
    }
    
    public function testBody(): void
    {
        $jsonSchema = new JsonSchema($this->tempFile);
        $body = $jsonSchema->body();
        
        // Check that the body contains the expected mapping code
        $this->assertIsString($body);
        // Verificando se o corpo contém os campos esperados, independente do tipo
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'title\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'description\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'price\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'quantity\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'is_active\'\);/', $body);
        $this->assertMatchesRegularExpression('/\$[a-z_]+->\w+\(\'tags\'\);/', $body);
        
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
        $jsonSchema = new JsonSchema($this->tempFile);
        $string = (string) $jsonSchema;
        
        $this->assertIsString($string);
        $this->assertStringContainsString('$index->keyword(\'title\');', $string);
    }
}
