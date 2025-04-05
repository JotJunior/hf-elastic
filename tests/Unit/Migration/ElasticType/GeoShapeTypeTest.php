<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\GeoShapeType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\GeoShapeType
 * @group unit
 * @internal
 */
class GeoShapeTypeTest extends TestCase
{
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\GeoShapeType::__construct
     * @group unit
     *
     * Test that the constructor properly initializes the GeoShapeType object
     *
     * What is being tested:
     * - The constructor of the GeoShapeType class
     * - The type property is set to Type::geoShape
     *
     * Conditions/Scenarios:
     * - Creating a new GeoShapeType instance
     *
     * Expected results:
     * - The type property should be set to Type::geoShape
     * - The name property should be set to the provided name
     * - The options array should be initialized
     */
    public function testConstructor(): void
    {
        // Act
        $type = new GeoShapeType('geo_shape_field');
        $options = $type->getOptions();

        // Assert
        $this->assertEquals(Type::geoShape, $type->getType(), 'Type should be set to geoShape');
        $this->assertEquals('geo_shape_field', $type->getName(), 'Field name should match the provided name');
        $this->assertIsArray($options, 'Options should be an array');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\GeoShapeType::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\GeoShapeType::orientation
     * @group unit
     *
     * Test that the orientation method properly sets the orientation option
     *
     * What is being tested:
     * - The orientation method of the GeoShapeType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting orientation to a string value
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The orientation option should be set to the provided value
     */
    public function testOrientation(): void
    {
        // Arrange
        $type = new GeoShapeType('geo_shape_field');
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
     * @covers \Jot\HfElastic\Migration\ElasticType\GeoShapeType::coerce
     * @covers \Jot\HfElastic\Migration\ElasticType\GeoShapeType::getOptions
     * @group unit
     *
     * Test that the coerce method properly sets the coerce option
     *
     * What is being tested:
     * - The coerce method of the GeoShapeType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting coerce to true
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The coerce option should be set to true
     */
    public function testCoerce(): void
    {
        // Arrange
        $type = new GeoShapeType('geo_shape_field');

        // Act
        $result = $type->coerce(true);
        $options = $type->getOptions();

        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['coerce'], 'coerce option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\GeoShapeType::getOptions
     * @group unit
     *
     * Test that the getOptions method returns all configured options
     *
     * What is being tested:
     * - The getOptions method of the GeoShapeType class when multiple options are set
     *
     * Conditions/Scenarios:
     * - Setting multiple options
     *
     * Expected results:
     * - The getOptions method should return all configured options with their correct values
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {
        // Arrange
        $type = new GeoShapeType('geo_shape_field');
        $orientation = 'ccw';

        // Act
        $type->orientation($orientation)
            ->coerce(true);

        $options = $type->getOptions();

        // Assert
        $this->assertEquals($orientation, $options['orientation'], 'orientation option should be set to the provided value');
        $this->assertTrue($options['coerce'], 'coerce option should be set to true');
    }
}
