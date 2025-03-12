<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\BooleanType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType
 * @group unit
 */
class BooleanTypeTest extends TestCase
{
    private BooleanType $type;
    
    protected function setUp(): void
    {
        $this->type = new BooleanType('test_field');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::__construct
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::getName
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::getType
     * @group unit
     *
     * Test that the constructor properly initializes the BooleanType
     *
     * What is being tested:
     * - The constructor of the BooleanType class
     * - The getName method returns the correct field name
     * - The getType method returns the correct type constant
     *
     * Conditions/Scenarios:
     * - Creating a new BooleanType with a specific field name
     *
     * Expected results:
     * - The field name should match the provided name
     * - The type should be set to Type::boolean
     *
     * @return void
     */
    public function testConstructor(): void
    {
        // Arrange - already done in setUp
        
        // Act & Assert
        $this->assertEquals('test_field', $this->type->getName(), 'Field name should match the provided name');
        $this->assertEquals(Type::boolean, $this->type->getType(), 'Type should be set to Type::boolean');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::boost
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::getOptions
     * @group unit
     *
     * Test that the boost method properly sets the boost option
     *
     * What is being tested:
     * - The boost method of the BooleanType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting a boost value of 1.5
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The boost option should be set to the provided value
     *
     * @return void
     */
    public function testBoost(): void
    {
        // Arrange
        $boostValue = 1.5;
        
        // Act
        $result = $this->type->boost($boostValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($boostValue, $options['boost'], 'Boost option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::docValues
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::getOptions
     * @group unit
     *
     * Test that the docValues method properly sets the doc_values option
     *
     * What is being tested:
     * - The docValues method of the BooleanType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting doc_values to false
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The doc_values option should be set to false
     *
     * @return void
     */
    public function testDocValues(): void
    {
        // Arrange
        $docValuesEnabled = false;
        
        // Act
        $result = $this->type->docValues($docValuesEnabled);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertFalse($options['doc_values'], 'doc_values option should be set to false');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::index
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::getOptions
     * @group unit
     *
     * Test that the index method properly sets the index option
     *
     * What is being tested:
     * - The index method of the BooleanType class
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
        $indexEnabled = false;
        
        // Act
        $result = $this->type->index($indexEnabled);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertFalse($options['index'], 'index option should be set to false');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::nullValue
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::getOptions
     * @group unit
     *
     * Test that the nullValue method properly sets the null_value option
     *
     * What is being tested:
     * - The nullValue method of the BooleanType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting null_value to true
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The null_value option should be set to true
     *
     * @return void
     */
    public function testNullValue(): void
    {
        // Arrange
        $nullValue = true;
        
        // Act
        $result = $this->type->nullValue($nullValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['null_value'], 'null_value option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::store
     * @covers \Jot\HfElastic\Migration\ElasticType\BooleanType::getOptions
     * @group unit
     *
     * Test that the store method properly sets the store option
     *
     * What is being tested:
     * - The store method of the BooleanType class
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
        $storeEnabled = true;
        
        // Act
        $result = $this->type->store($storeEnabled);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['store'], 'store option should be set to true');
    }
    

}
