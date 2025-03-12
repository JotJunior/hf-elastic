<?php

namespace Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\RangeType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\RangeType
 * @group unit
 */
class RangeTypeTest extends TestCase
{
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\RangeType::__construct
     * @group unit
     * 
     * Test that the constructor properly initializes the RangeType object
     * 
     * What is being tested:
     * - The constructor of the RangeType class
     * 
     * Conditions/Scenarios:
     * - Creating a new RangeType instance
     * 
     * Expected results:
     * - The name property should be set to the provided name
     * - The options array should be initialized
     * 
     * @return void
     */
    public function testConstructor(): void
    {
        // Act
        $type = new RangeType('range_field');
        $options = $type->getOptions();
        
        // Assert
        $this->assertEquals('range_field', $type->getName(), 'Field name should match the provided name');
        $this->assertIsArray($options, 'Options should be an array');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\RangeType::coerce
     * @covers \Jot\HfElastic\Migration\ElasticType\RangeType::getOptions
     * @group unit
     * 
     * Test that the coerce method properly sets the coerce option
     * 
     * What is being tested:
     * - The coerce method of the RangeType class
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
        $type = new RangeType('range_field');
        
        // Act
        $result = $type->coerce(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['coerce'], 'coerce option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\RangeType::docValues
     * @covers \Jot\HfElastic\Migration\ElasticType\RangeType::getOptions
     * @group unit
     * 
     * Test that the docValues method properly sets the doc_values option
     * 
     * What is being tested:
     * - The docValues method of the RangeType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting doc_values to true
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The doc_values option should be set to true
     * 
     * @return void
     */
    public function testDocValues(): void
    {
        // Arrange
        $type = new RangeType('range_field');
        
        // Act
        $result = $type->docValues(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['doc_values'], 'doc_values option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\RangeType::index
     * @covers \Jot\HfElastic\Migration\ElasticType\RangeType::getOptions
     * @group unit
     * 
     * Test that the index method properly sets the index option
     * 
     * What is being tested:
     * - The index method of the RangeType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting index to false
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The index option should be set to false
     * 
     * @return void
     */
    public function testIndex(): void
    {
        // Arrange
        $type = new RangeType('range_field');
        
        // Act
        $result = $type->index(false);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertFalse($options['index'], 'index option should be set to false');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\RangeType::store
     * @covers \Jot\HfElastic\Migration\ElasticType\RangeType::getOptions
     * @group unit
     * 
     * Test that the store method properly sets the store option
     * 
     * What is being tested:
     * - The store method of the RangeType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting store to true
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The store option should be set to true
     * 
     * @return void
     */
    public function testStore(): void
    {
        // Arrange
        $type = new RangeType('range_field');
        
        // Act
        $result = $type->store(true);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['store'], 'store option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\RangeType::getOptions
     * @group unit
     * 
     * Test that the getOptions method returns all configured options
     * 
     * What is being tested:
     * - The getOptions method of the RangeType class when multiple options are set
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
        $type = new RangeType('range_field');
        
        // Act
        $type->coerce(true)
            ->docValues(false)
            ->index(true)
            ->store(false);
        
        $options = $type->getOptions();
        
        // Assert
        $this->assertTrue($options['coerce'], 'coerce option should be set to true');
        $this->assertFalse($options['doc_values'], 'doc_values option should be set to false');
        $this->assertTrue($options['index'], 'index option should be set to true');
        $this->assertFalse($options['store'], 'store option should be set to false');
    }
}
