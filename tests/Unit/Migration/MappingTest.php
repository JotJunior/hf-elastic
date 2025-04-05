<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\Unit\Migration;

use Jot\HfElastic\Migration\ElasticType\KeywordType;
use Jot\HfElastic\Migration\ElasticType\NestedType;
use Jot\HfElastic\Migration\ElasticType\ObjectType;
use Jot\HfElastic\Migration\ElasticType\TextType;
use Jot\HfElastic\Migration\ElasticType\Type;
use Jot\HfElastic\Migration\Mapping;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class MappingTest extends TestCase
{
    private Mapping $mapping;

    protected function setUp(): void
    {
        $this->mapping = new Mapping('test_index');
    }

    public function testSetName(): void
    {
        $this->mapping->setName('new_index');
        $this->assertEquals('new_index', $this->mapping->getName());
    }

    public function testSettings(): void
    {
        $settings = [
            'number_of_shards' => 3,
            'number_of_replicas' => 2,
        ];

        $result = $this->mapping->settings($settings);

        $this->assertSame($this->mapping, $result);
        $this->assertEquals($settings, $this->mapping->body()['body']['settings']);
    }

    public function testProperty(): void
    {
        $result = $this->mapping->property('title', Type::text, ['analyzer' => 'standard']);

        $this->assertSame($this->mapping, $result);
        $this->assertArrayHasKey('title', $this->mapping->body()['body']['mappings']['properties']);
        $this->assertEquals('text', $this->mapping->body()['body']['mappings']['properties']['title']['type']);
        $this->assertEquals('standard', $this->mapping->body()['body']['mappings']['properties']['title']['analyzer']);
    }

    public function testBody(): void
    {
        $this->mapping->property('title', Type::text);
        $this->mapping->settings(['number_of_shards' => 3]);

        $body = $this->mapping->body();

        $this->assertArrayHasKey('index', $body);
        $this->assertEquals('test_index', $body['index']);
        $this->assertArrayHasKey('body', $body);
        $this->assertArrayHasKey('settings', $body['body']);
        $this->assertArrayHasKey('mappings', $body['body']);
        $this->assertArrayHasKey('dynamic', $body['body']['mappings']);
        $this->assertEquals('strict', $body['body']['mappings']['dynamic']);
        $this->assertArrayHasKey('properties', $body['body']['mappings']);
    }

    public function testUpdateBody(): void
    {
        $this->mapping->property('title', Type::text);

        $body = $this->mapping->updateBody();

        $this->assertArrayHasKey('index', $body);
        $this->assertEquals('test_index', $body['index']);
        $this->assertArrayHasKey('body', $body);
        $this->assertArrayHasKey('properties', $body['body']);
    }

    public function testGenerateMapping(): void
    {
        // Add a text field
        $textField = new TextType('description');
        $this->mapping->text('description');

        // Add a keyword field
        $keywordField = new KeywordType('tag');
        $this->mapping->keyword('tag');

        // Generate mapping
        $mapping = $this->mapping->generateMapping();

        // Assertions
        $this->assertArrayHasKey('properties', $mapping);
        $this->assertArrayHasKey('description', $mapping['properties']);
        $this->assertEquals('text', $mapping['properties']['description']['type']);
        $this->assertArrayHasKey('tag', $mapping['properties']);
        $this->assertEquals('keyword', $mapping['properties']['tag']['type']);
    }

    public function testNestedFields(): void
    {
        // Create a nested field
        $nested = new NestedType('comments');
        $nested->text('content');
        $nested->keyword('author');

        // Add the nested field to the mapping
        $this->mapping->nested($nested);

        // Generate mapping
        $mapping = $this->mapping->generateMapping();

        // Assertions
        $this->assertArrayHasKey('properties', $mapping);
        $this->assertArrayHasKey('comments', $mapping['properties']);
        $this->assertEquals('nested', $mapping['properties']['comments']['type']);
        $this->assertArrayHasKey('properties', $mapping['properties']['comments']);
        $this->assertArrayHasKey('content', $mapping['properties']['comments']['properties']);
        $this->assertEquals('text', $mapping['properties']['comments']['properties']['content']['type']);
        $this->assertArrayHasKey('author', $mapping['properties']['comments']['properties']);
        $this->assertEquals('keyword', $mapping['properties']['comments']['properties']['author']['type']);
    }

    public function testObjectFields(): void
    {
        // Create an object field
        $object = new ObjectType('metadata');
        $object->keyword('category');
        $object->date('published_at');

        // Add the object field to the mapping
        $this->mapping->object($object);

        // Generate mapping
        $mapping = $this->mapping->generateMapping();

        // Assertions
        $this->assertArrayHasKey('properties', $mapping);
        $this->assertArrayHasKey('metadata', $mapping['properties']);
        $this->assertArrayHasKey('properties', $mapping['properties']['metadata']);
        $this->assertArrayHasKey('category', $mapping['properties']['metadata']['properties']);
        $this->assertEquals('keyword', $mapping['properties']['metadata']['properties']['category']['type']);
        $this->assertArrayHasKey('published_at', $mapping['properties']['metadata']['properties']);
        $this->assertEquals('date', $mapping['properties']['metadata']['properties']['published_at']['type']);
    }
}
