<?php

namespace Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\ShapeType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\ShapeType
 * @group unit
 */
class ShapeTypeTest extends TestCase
{
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ShapeType::__construct
     * @group unit
     * 
     * Test that the constructor properly initializes the ShapeType object
     * 
     * What is being tested:
     * - The constructor of the ShapeType class
     * - The type property is set to Type::shape
     * 
     * Conditions/Scenarios:
     * - Creating a new ShapeType instance
     * 
     * Expected results:
     * - The type property should be set to Type::shape
     * - The name property should be set to the provided name
     * - The options array should be initialized
     * 
     * @return void
     */
    public function testConstructor(): void
    {
        // Act
        $type = new ShapeType('shape_field');
        $options = $type->getOptions();
        
        // Assert
        $this->assertEquals(Type::shape, $type->getType(), 'Type should be set to shape');
        $this->assertEquals('shape_field', $type->getName(), 'Field name should match the provided name');
        $this->assertIsArray($options, 'Options should be an array');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ShapeType::orientation
     * @covers \Jot\HfElastic\Migration\ElasticType\ShapeType::getOptions
     * @group unit
     * 
     * Test that the orientation method properly sets the orientation option
     * 
     * What is being tested:
     * - The orientation method of the ShapeType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting orientation to a string value
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The orientation option should be set to the provided value
     * 
     * @return void
     */
    public function testOrientation(): void
    {
        // Arrange
        $type = new ShapeType('shape_field');
        $orientation = 'ccw';
        
        // Act
        $result = $type->orientation($orientation);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($orientation, $options['orientation'], 'orientation option should be set to the provided value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ShapeType::ignoreMalformed
     * @covers \Jot\HfElastic\Migration\ElasticType\ShapeType::getOptions
     * @group unit
     * 
     * Test that the ignoreMalformed method properly sets the ignore_malformed option
     * 
     * What is being tested:
     * - The ignoreMalformed method of the ShapeType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting ignore_malformed to true
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The ignore_malformed option should be set to true
     * 
     * @return void
     */
    public function testIgnoreMalformed(): void
    {
        // Arrange
        $type = new ShapeType('shape_field');
        
        // Act
        $result = $type->ignoreMalformed(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['ignore_malformed'], 'ignore_malformed option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ShapeType::ignoreZValue
     * @covers \Jot\HfElastic\Migration\ElasticType\ShapeType::getOptions
     * @group unit
     * 
     * Test that the ignoreZValue method properly sets the ignore_z_value option
     * 
     * What is being tested:
     * - The ignoreZValue method of the ShapeType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting ignore_z_value to true
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The ignore_z_value option should be set to true
     * 
     * @return void
     */
    public function testIgnoreZValue(): void
    {
        // Arrange
        $type = new ShapeType('shape_field');
        
        // Act
        $result = $type->ignoreZValue(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['ignore_z_value'], 'ignore_z_value option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ShapeType::coerce
     * @covers \Jot\HfElastic\Migration\ElasticType\ShapeType::getOptions
     * @group unit
     * 
     * Test that the coerce method properly sets the coerce option
     * 
     * What is being tested:
     * - The coerce method of the ShapeType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting coerce to true
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The coerce option should be set to true
     * 
     * @return void
     */
    public function testCoerce(): void
    {
        // Arrange
        $type = new ShapeType('shape_field');
        
        // Act
        $result = $type->coerce(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['coerce'], 'coerce option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\ShapeType::getOptions
     * @group unit
     * 
     * Test that the getOptions method returns all configured options
     * 
     * What is being tested:
     * - The getOptions method of the ShapeType class when multiple options are set
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
        $type = new ShapeType('shape_field');
        $orientation = 'ccw';
        
        // Act
        $type->orientation($orientation)
            ->ignoreMalformed(true)
            ->ignoreZValue(false)
            ->coerce(true);
        
        $options = $type->getOptions();
        
        // Assert
        $this->assertEquals($orientation, $options['orientation'], 'orientation option should be set to the provided value');
        $this->assertTrue($options['ignore_malformed'], 'ignore_malformed option should be set to true');
        $this->assertFalse($options['ignore_z_value'], 'ignore_z_value option should be set to false');
        $this->assertTrue($options['coerce'], 'coerce option should be set to true');
    }
}
