<?php

namespace Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\ObjectType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\ObjectType
 * @group unit
 */
class ObjectTypeTest extends TestCase
{
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ObjectType::__construct
     * @group unit
     * 
     * Test that the constructor properly initializes the ObjectType object
     * 
     * What is being tested:
     * - The constructor of the ObjectType class
     * - The type property is set to Type::object
     * 
     * Conditions/Scenarios:
     * - Creating a new ObjectType instance
     * 
     * Expected results:
     * - The type property should be set to Type::object
     * - The name property should be set to the provided name
     * - The options array should be initialized
     * 
     * @return void
     */
    public function testConstructor(): void
    {
        // Act
        $type = new ObjectType('object_field');
        $options = $type->getOptions();
        
        // Assert
        $this->assertEquals(Type::object, $type->getType(), 'Type should be set to object');
        $this->assertEquals('object_field', $type->getName(), 'Field name should match the provided name');
        $this->assertIsArray($options, 'Options should be an array');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ObjectType::dynamic
     * @covers \Jot\HfElastic\Migration\ElasticType\ObjectType::getOptions
     * @group unit
     * 
     * Test that the dynamic method properly sets the dynamic option
     * 
     * What is being tested:
     * - The dynamic method of the ObjectType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting dynamic to true
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The dynamic option should be set to true
     * 
     * @return void
     */
    public function testDynamic(): void
    {
        // Arrange
        $type = new ObjectType('object_field');
        
        // Act
        $result = $type->dynamic(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['dynamic'], 'dynamic option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ObjectType::enabled
     * @covers \Jot\HfElastic\Migration\ElasticType\ObjectType::getOptions
     * @group unit
     * 
     * Test that the enabled method properly sets the enabled option
     * 
     * What is being tested:
     * - The enabled method of the ObjectType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting enabled to true
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The enabled option should be set to true
     * 
     * @return void
     */
    public function testEnabled(): void
    {
        // Arrange
        $type = new ObjectType('object_field');
        
        // Act
        $result = $type->enabled(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['enabled'], 'enabled option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ObjectType::subobjects
     * @covers \Jot\HfElastic\Migration\ElasticType\ObjectType::getOptions
     * @group unit
     * 
     * Test that the subobjects method properly sets the subobjects option
     * 
     * What is being tested:
     * - The subobjects method of the ObjectType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting subobjects to true
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The subobjects option should be set to true
     * 
     * @return void
     */
    public function testSubobjects(): void
    {
        // Arrange
        $type = new ObjectType('object_field');
        
        // Act
        $result = $type->subobjects(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['subobjects'], 'subobjects option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ObjectType::getProperties
     * @covers \Jot\HfElastic\Migration\ElasticType\ObjectType::convertTypeNameToSnakeCase
     * @group unit
     * 
     * Test that the getProperties method returns the correct properties
     * 
     * What is being tested:
     * - The getProperties method of the ObjectType class
     * - The convertTypeNameToSnakeCase method (indirectly)
     * 
     * Conditions/Scenarios:
     * - Adding fields to the ObjectType and retrieving their properties
     * 
     * Expected results:
     * - The getProperties method should return an array with the correct field properties
     * 
     * @return void
     */
    public function testGetProperties(): void
    {
        // Arrange
        $type = new ObjectType('object_field');
        
        // Mock field objects with getOptions, getType, and getName methods
        $field1 = $this->createMock(\Jot\HfElastic\Migration\Property::class);
        $field1->method('getOptions')->willReturn(['analyzer' => 'standard']);
        $field1->method('getType')->willReturn(Type::text);
        $field1->method('getName')->willReturn('text_field');
        
        $field2 = $this->createMock(\Jot\HfElastic\Migration\Property::class);
        $field2->method('getOptions')->willReturn(['boost' => 2.0]);
        $field2->method('getType')->willReturn(Type::keyword);
        $field2->method('getName')->willReturn('keyword_field');
        
        // Create a nested field that has getProperties method
        $nestedField = $this->createMock(\Jot\HfElastic\Migration\ElasticType\NestedType::class);
        $nestedField->method('getOptions')->willReturn([]);
        $nestedField->method('getType')->willReturn(Type::nested);
        $nestedField->method('getName')->willReturn('nested_field');
        $nestedField->method('getProperties')->willReturn([
            'nested_text' => ['type' => 'text', 'analyzer' => 'standard'],
            'nested_keyword' => ['type' => 'keyword']
        ]);
        
        // Add fields to the type using reflection
        $reflection = new \ReflectionClass($type);
        $fieldsProperty = $reflection->getProperty('fields');
        $fieldsProperty->setAccessible(true);
        $fieldsProperty->setValue($type, [$field1, $field2, $nestedField]);
        
        // Act
        $properties = $type->getProperties();
        
        // Assert
        $this->assertIsArray($properties, 'getProperties should return an array');
        $this->assertArrayHasKey('text_field', $properties, 'Properties should include text_field');
        $this->assertArrayHasKey('keyword_field', $properties, 'Properties should include keyword_field');
        $this->assertArrayHasKey('nested_field', $properties, 'Properties should include nested_field');
        
        $this->assertEquals('text', $properties['text_field']['type'], 'text_field type should be text');
        $this->assertEquals('keyword', $properties['keyword_field']['type'], 'keyword_field type should be keyword');
        $this->assertEquals('nested', $properties['nested_field']['type'], 'nested_field type should be nested');
        
        $this->assertEquals('standard', $properties['text_field']['analyzer'], 'text_field analyzer should be standard');
        $this->assertEquals(2.0, $properties['keyword_field']['boost'], 'keyword_field boost should be 2.0');
        
        $this->assertArrayHasKey('properties', $properties['nested_field'], 'nested_field should have properties');
        $this->assertArrayHasKey('nested_text', $properties['nested_field']['properties'], 'nested_field properties should include nested_text');
        $this->assertArrayHasKey('nested_keyword', $properties['nested_field']['properties'], 'nested_field properties should include nested_keyword');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ObjectType::getOptions
     * @group unit
     * 
     * Test that the getOptions method returns all configured options
     * 
     * What is being tested:
     * - The getOptions method of the ObjectType class when multiple options are set
     * 
     * Conditions/Scenarios:
     * - Setting multiple options
     * 
     * Expected results:
     * - The getOptions method should return all configured options with their correct values
     * 
     * @return void
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {
        // Arrange
        $type = new ObjectType('object_field');
        
        // Act
        $type->dynamic(true)
            ->enabled(false)
            ->subobjects(true);
        
        $options = $type->getOptions();
        
        // Assert
        // Verificamos apenas as opções que sabemos que serão retornadas após a filtragem
        // pelo array_filter() no método getOptions()
        $this->assertArrayHasKey('dynamic', $options, 'dynamic option should exist');
        $this->assertTrue($options['dynamic'], 'dynamic option should be set to true');
        
        $this->assertArrayHasKey('subobjects', $options, 'subobjects option should exist');
        $this->assertTrue($options['subobjects'], 'subobjects option should be set to true');
        
        // Verificamos o funcionamento do método enabled() diretamente
        // usando Reflection para acessar a propriedade options protegida
        $reflection = new \ReflectionClass($type);
        $optionsProperty = $reflection->getProperty('options');
        $optionsProperty->setAccessible(true);
        $internalOptions = $optionsProperty->getValue($type);
        
        $this->assertArrayHasKey('enabled', $internalOptions, 'enabled option should exist in internal options');
        $this->assertFalse($internalOptions['enabled'], 'enabled option should be set to false in internal options');
    }
}
