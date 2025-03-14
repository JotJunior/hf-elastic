<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\IpType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\IpType
 * @group unit
 */
class IpTypeTest extends TestCase
{
    private IpType $type;
    
    protected function setUp(): void
    {
        $this->type = new IpType('test_field');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::__construct
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::getName
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::getType
     * @group unit
     * Test that the constructor properly initializes the IpType
     * What is being tested:
     * - The constructor of the IpType class
     * - The getName method returns the correct field name
     * - The getType method returns the correct type constant
     * Conditions/Scenarios:
     * - Creating a new IpType with a specific field name
     * Expected results:
     * - The field name should match the provided name
     * - The type should be set to Type::ip
     * @return void
     */
    public function testConstructor(): void
    {
        // Arrange - already done in setUp
        
        // Act & Assert
        $this->assertEquals('test_field', $this->type->getName(), 'Field name should match the provided name');
        $this->assertEquals(Type::ip, $this->type->getType(), 'Type should be set to Type::ip');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::docValues
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::getOptions
     * @group unit
     * Test that the docValues method properly sets the doc_values option
     * What is being tested:
     * - The docValues method of the IpType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting doc_values to false
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The doc_values option should be set to false
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
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::ignoreMalformed
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::getOptions
     * @group unit
     * Test that the ignoreMalformed method properly sets the ignore_malformed option
     * What is being tested:
     * - The ignoreMalformed method of the IpType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting ignore_malformed to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The ignore_malformed option should be set to true
     * @return void
     */
    public function testIgnoreMalformed(): void
    {
        // Arrange
        $ignoreMalformed = true;
        
        // Act
        $result = $this->type->ignoreMalformed($ignoreMalformed);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['ignore_malformed'], 'ignore_malformed option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::index
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::getOptions
     * @group unit
     * Test that the index method properly sets the index option
     * What is being tested:
     * - The index method of the IpType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting index to false
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The index option should be set to false
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
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::nullValue
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::getOptions
     * @group unit
     * Test that the nullValue method properly sets the null_value option
     * What is being tested:
     * - The nullValue method of the IpType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting null_value to a specific IP address
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The null_value option should be set to the provided IP address
     * @return void
     */
    public function testNullValue(): void
    {
        // Arrange
        $nullValue = '0.0.0.0';
        
        // Act
        $result = $this->type->nullValue($nullValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($nullValue, $options['null_value'], 'null_value option should be set to the provided IP address');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::onScriptError
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::getOptions
     * @group unit
     * Test that the onScriptError method properly sets the on_script_error option
     * What is being tested:
     * - The onScriptError method of the IpType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting on_script_error to 'fail'
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The on_script_error option should be set to 'fail'
     * @return void
     */
    public function testOnScriptError(): void
    {
        // Arrange
        $onScriptError = 'fail';
        
        // Act
        $result = $this->type->onScriptError($onScriptError);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($onScriptError, $options['on_script_error'], 'on_script_error option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::script
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::getOptions
     * @group unit
     * Test that the script method properly sets the script option
     * What is being tested:
     * - The script method of the IpType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting script to a specific script value
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The script option should be set to the provided script value
     * @return void
     */
    public function testScript(): void
    {
        // Arrange
        $script = 'ctx._source.ip = params.ip';
        
        // Act
        $result = $this->type->script($script);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($script, $options['script'], 'script option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::store
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::getOptions
     * @group unit
     * Test that the store method properly sets the store option
     * What is being tested:
     * - The store method of the IpType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting store to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The store option should be set to true
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
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::timeSeriesDimension
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::getOptions
     * @group unit
     * Test that the timeSeriesDimension method properly sets the time_series_dimension option
     * What is being tested:
     * - The timeSeriesDimension method of the IpType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting time_series_dimension to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The time_series_dimension option should be set to true
     * @return void
     */
    public function testTimeSeriesDimension(): void
    {
        // Arrange
        $timeSeriesDimension = true;
        
        // Act
        $result = $this->type->timeSeriesDimension($timeSeriesDimension);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['time_series_dimension'], 'time_series_dimension option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\IpType::getOptions
     * @group unit
     * Test that the getOptions method returns all configured options
     * What is being tested:
     * - The getOptions method of the IpType class when multiple options are set
     * Conditions/Scenarios:
     * - Setting multiple options (doc_values, ignore_malformed, index, null_value, etc.)
     * Expected results:
     * - The getOptions method should return all configured options with their correct values
     * @return void
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {
        // Arrange
        $this->type->docValues(false)
            ->ignoreMalformed(true)
            ->index(false)
            ->nullValue('0.0.0.0')
            ->onScriptError('fail')
            ->script('ctx._source.ip = params.ip')
            ->store(true)
            ->timeSeriesDimension(true);
        
        // Act
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertFalse($options['doc_values'], 'doc_values option should be set to false');
        $this->assertTrue($options['ignore_malformed'], 'ignore_malformed option should be set to true');
        $this->assertFalse($options['index'], 'index option should be set to false');
        $this->assertEquals('0.0.0.0', $options['null_value'], 'null_value option should be set to the provided IP address');
        $this->assertEquals('fail', $options['on_script_error'], 'on_script_error option should be set to the provided value');
        $this->assertEquals('ctx._source.ip = params.ip', $options['script'], 'script option should be set to the provided value');
        $this->assertTrue($options['store'], 'store option should be set to true');
        $this->assertTrue($options['time_series_dimension'], 'time_series_dimension option should be set to true');
    }
}
