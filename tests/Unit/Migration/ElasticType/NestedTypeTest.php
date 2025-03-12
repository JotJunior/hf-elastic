<?php

namespace Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\NestedType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\NestedType
 * @group unit
 */
class NestedTypeTest extends TestCase
{
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::__construct
     * @group unit
     * 
     * Test that the constructor properly initializes the NestedType object
     * 
     * What is being tested:
     * - The constructor of the NestedType class
     * - The type property is set to Type::nested
     * 
     * Conditions/Scenarios:
     * - Creating a new NestedType instance
     * 
     * Expected results:
     * - The type property should be set to Type::nested
     * - The name property should be set to the provided name
     * - The options array should be initialized
     * 
     * @return void
     */
    public function testConstructor(): void
    {
        // Act
        $type = new NestedType('nested_field');
        $options = $type->getOptions();
        
        // Assert
        $this->assertEquals(Type::nested, $type->getType(), 'Type should be set to nested');
        $this->assertEquals('nested_field', $type->getName(), 'Field name should match the provided name');
        $this->assertIsArray($options, 'Options should be an array');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::dynamic
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::getOptions
     * @group unit
     * 
     * Test that the dynamic method properly sets the dynamic option
     * 
     * What is being tested:
     * - The dynamic method of the NestedType class
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
        $type = new NestedType('nested_field');
        
        // Act
        $result = $type->dynamic(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['dynamic'], 'dynamic option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::properties
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::getOptions
     * @group unit
     * 
     * Test that the properties method properly sets the properties option
     * 
     * What is being tested:
     * - The properties method of the NestedType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting properties to an array of properties
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The properties option should be set to the provided array
     * 
     * @return void
     */
    public function testProperties(): void
    {
        // Arrange
        $type = new NestedType('nested_field');
        $properties = ['field1' => ['type' => 'text'], 'field2' => ['type' => 'keyword']];
        
        // Act
        $result = $type->properties($properties);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($properties, $options['properties'], 'properties option should be set to the provided array');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::includeInParent
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::getOptions
     * @group unit
     * 
     * Test that the includeInParent method properly sets the include_in_parent option
     * 
     * What is being tested:
     * - The includeInParent method of the NestedType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting include_in_parent to true
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The include_in_parent option should be set to true
     * 
     * @return void
     */
    public function testIncludeInParent(): void
    {
        // Arrange
        $type = new NestedType('nested_field');
        
        // Act
        $result = $type->includeInParent(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['include_in_parent'], 'include_in_parent option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::includeInRoot
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::getOptions
     * @group unit
     * 
     * Test that the includeInRoot method properly sets the include_in_root option
     * 
     * What is being tested:
     * - The includeInRoot method of the NestedType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting include_in_root to true
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The include_in_root option should be set to true
     * 
     * @return void
     */
    public function testIncludeInRoot(): void
    {
        // Arrange
        $type = new NestedType('nested_field');
        
        // Act
        $result = $type->includeInRoot(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['include_in_root'], 'include_in_root option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::getProperties
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::convertTypeNameToSnakeCase
     * @group unit
     * 
     * Test that the getProperties method returns the correct properties
     * 
     * What is being tested:
     * - The getProperties method of the NestedType class
     * - The convertTypeNameToSnakeCase method (indirectly)
     * 
     * Conditions/Scenarios:
     * - Adding fields to the NestedType and retrieving their properties
     * 
     * Expected results:
     * - The getProperties method should return an array with the correct field properties
     * 
     * @return void
     */
    public function testGetProperties(): void
    {
        // Arrange
        $type = new NestedType('nested_field');
        
        // Mock field objects with getOptions, getType, and getName methods
        $field1 = $this->createMock(\Jot\HfElastic\Migration\Property::class);
        $field1->method('getOptions')->willReturn(['analyzer' => 'standard']);
        $field1->method('getType')->willReturn(Type::text);
        $field1->method('getName')->willReturn('text_field');
        
        $field2 = $this->createMock(\Jot\HfElastic\Migration\Property::class);
        $field2->method('getOptions')->willReturn(['boost' => 2.0]);
        $field2->method('getType')->willReturn(Type::keyword);
        $field2->method('getName')->willReturn('keyword_field');
        
        // Add fields to the type using reflection
        $reflection = new \ReflectionClass($type);
        $fieldsProperty = $reflection->getProperty('fields');
        $fieldsProperty->setAccessible(true);
        $fieldsProperty->setValue($type, [$field1, $field2]);
        
        // Act
        $properties = $type->getProperties();
        
        // Assert
        $this->assertIsArray($properties, 'getProperties should return an array');
        $this->assertArrayHasKey('text_field', $properties, 'Properties should include text_field');
        $this->assertArrayHasKey('keyword_field', $properties, 'Properties should include keyword_field');
        $this->assertEquals('text', $properties['text_field']['type'], 'text_field type should be text');
        $this->assertEquals('keyword', $properties['keyword_field']['type'], 'keyword_field type should be keyword');
        $this->assertEquals('standard', $properties['text_field']['analyzer'], 'text_field analyzer should be standard');
        $this->assertEquals(2.0, $properties['keyword_field']['boost'], 'keyword_field boost should be 2.0');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\NestedType::getOptions
     * @group unit
     * 
     * Test that the getOptions method returns all configured options
     * 
     * What is being tested:
     * - The getOptions method of the NestedType class when multiple options are set
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
        $type = new NestedType('nested_field');
        $properties = ['field1' => ['type' => 'text'], 'field2' => ['type' => 'keyword']];
        
        // Act
        $type->dynamic(true)
            ->properties($properties)
            ->includeInParent(true)
            ->includeInRoot(false);
        
        $options = $type->getOptions();
        
        // Assert
        // Verificamos apenas as opções que sabemos que serão retornadas após a filtragem
        // pelo array_filter() no método getOptions()
        $this->assertArrayHasKey('dynamic', $options, 'dynamic option should exist');
        $this->assertTrue($options['dynamic'], 'dynamic option should be set to true');
        
        $this->assertArrayHasKey('properties', $options, 'properties option should exist');
        $this->assertEquals($properties, $options['properties'], 'properties option should be set to the provided array');
        
        $this->assertArrayHasKey('include_in_parent', $options, 'include_in_parent option should exist');
        $this->assertTrue($options['include_in_parent'], 'include_in_parent option should be set to true');
        
        // Verificamos o funcionamento do método includeInRoot() diretamente
        // usando Reflection para acessar a propriedade options protegida
        $reflection = new \ReflectionClass($type);
        $optionsProperty = $reflection->getProperty('options');
        $optionsProperty->setAccessible(true);
        $internalOptions = $optionsProperty->getValue($type);
        
        $this->assertArrayHasKey('include_in_root', $internalOptions, 'include_in_root option should exist in internal options');
        $this->assertFalse($internalOptions['include_in_root'], 'include_in_root option should be set to false in internal options');
    }
}
