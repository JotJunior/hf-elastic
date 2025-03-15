<?php

namespace Jot\HfElastic\Tests\Unit\Migration;

use Jot\HfElastic\Migration\ElasticType\KeywordType;
use Jot\HfElastic\Migration\ElasticType\NestedType;
use Jot\HfElastic\Migration\ElasticType\ObjectType;
use Jot\HfElastic\Migration\ElasticType\TextType;
use Jot\HfElastic\Migration\ElasticType\Type;
use Jot\HfElastic\Migration\Property;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    private Property $property;

    public function testGetName(): void
    {
        $this->assertEquals('test_property', $this->property->getName());
    }

    public function testGetType(): void
    {
        $this->assertEquals(Type::object, $this->property->getType());
    }

    public function testGetOptions(): void
    {
        $this->assertIsArray($this->property->getOptions());
        $this->assertEmpty($this->property->getOptions());
    }

    public function testObject(): void
    {
        $object = new ObjectType('metadata');
        $result = $this->property->object($object);

        $this->assertSame($object, $result);
        $this->assertContains($object, $this->property->getChildren());
    }

    public function testNested(): void
    {
        $nested = new NestedType('comments');
        $result = $this->property->nested($nested);

        $this->assertSame($nested, $result);
        $this->assertContains($nested, $this->property->getChildren());
    }

    public function testText(): void
    {
        $text = $this->property->text('content');

        $this->assertInstanceOf(TextType::class, $text);
        $this->assertEquals('content', $text->getName());
        $this->assertEquals(Type::text, $text->getType());
        $this->assertContains($text, $this->property->getChildren());
    }

    public function testKeyword(): void
    {
        $keyword = $this->property->keyword('tag');

        $this->assertInstanceOf(KeywordType::class, $keyword);
        $this->assertEquals('tag', $keyword->getName());
        $this->assertEquals(Type::keyword, $keyword->getType());
        $this->assertContains($keyword, $this->property->getChildren());
    }

    public function testDefaults(): void
    {
        $this->property->defaults();
        $children = $this->property->getChildren();

        $this->assertCount(5, $children);

        $fieldNames = array_map(function ($field) {
            return $field->getName();
        }, $children);

        $this->assertContains('created_at', $fieldNames);
        $this->assertContains('updated_at', $fieldNames);
        $this->assertContains('deleted', $fieldNames);
        $this->assertContains('@version', $fieldNames);
        $this->assertContains('@timestamp', $fieldNames);
    }

    public function testMultipleFieldTypes(): void
    {
        // Test a few more field types
        $date = $this->property->date('published_at');
        $boolean = $this->property->boolean('active');
        $integer = $this->property->addField('integer', 'count');
        $float = $this->property->addField('float', 'price');
        $ip = $this->property->addField('ip', 'ip_address');

        $children = $this->property->getChildren();

        $this->assertCount(5, $children);
        $this->assertEquals(Type::date, $date->getType());
        $this->assertEquals(Type::boolean, $boolean->getType());
        $this->assertEquals(Type::integer, $integer->getType());
        $this->assertEquals(Type::float, $float->getType());
        $this->assertEquals(Type::ip, $ip->getType());
    }

    protected function setUp(): void
    {
        $this->property = new Property('test_property');
    }
}
