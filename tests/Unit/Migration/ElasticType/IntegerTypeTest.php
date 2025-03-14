<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\IntegerType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\IntegerType
 * @covers \Jot\HfElastic\Migration\ElasticType\Numeric
 * @group unit
 */
class IntegerTypeTest extends TestCase
{
    private IntegerType $type;
    
    protected function setUp(): void
    {
        $this->type = new IntegerType('test_field');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\IntegerType::__construct
     * @covers \Jot\HfElastic\Migration\ElasticType\IntegerType::getName
     * @covers \Jot\HfElastic\Migration\ElasticType\IntegerType::getType
     * @group unit
     * Test that the constructor properly initializes the IntegerType
     * What is being tested:
     * - The constructor of the IntegerType class
     * - The getName method returns the correct field name
     * - The getType method returns the correct type constant
     * Conditions/Scenarios:
     * - Creating a new IntegerType with a specific field name
     * Expected results:
     * - The field name should match the provided name
     * - The type should be set to Type::integer
     * @return void
     */
    public function testConstructor(): void
    {
        // Arrange - already done in setUp
        
        // Act & Assert
        $this->assertEquals('test_field', $this->type->getName(), 'Field name should match the provided name');
        $this->assertEquals(Type::integer, $this->type->getType(), 'Type should be set to Type::integer');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::coerce
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the coerce method properly sets the coerce option
     * What is being tested:
     * - The coerce method of the Numeric class (inherited by IntegerType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting coerce to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The coerce option should be set to true
     * @return void
     */
    public function testCoerce(): void
    {
        // Arrange
        $coerceEnabled = true;
        
        // Act
        $result = $this->type->coerce($coerceEnabled);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['coerce'], 'coerce option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::docValues
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the docValues method properly sets the doc_values option
     * What is being tested:
     * - The docValues method of the Numeric class (inherited by IntegerType)
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
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::ignoreMalformed
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the ignoreMalformed method properly sets the ignore_malformed option
     * What is being tested:
     * - The ignoreMalformed method of the Numeric class (inherited by IntegerType)
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
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::index
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the index method properly sets the index option
     * What is being tested:
     * - The index method of the Numeric class (inherited by IntegerType)
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
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::meta
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the meta method properly sets the meta option
     * What is being tested:
     * - The meta method of the Numeric class (inherited by IntegerType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting meta to an array of metadata
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The meta option should be set to the provided array
     * @return void
     */
    public function testMeta(): void
    {
        // Arrange
        $metaData = ['description' => 'Test integer field'];
        
        // Act
        $result = $this->type->meta($metaData);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($metaData, $options['meta'], 'meta option should be set to the provided array');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::nullValue
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the nullValue method properly sets the null_value option
     * What is being tested:
     * - The nullValue method of the Numeric class (inherited by IntegerType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting null_value to an integer value
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The null_value option should be set to the provided integer
     * @return void
     */
    public function testNullValue(): void
    {
        // Arrange
        $nullValue = 0;
        
        // Act
        $result = $this->type->nullValue($nullValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($nullValue, $options['null_value'], 'null_value option should be set to the provided integer');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::onScriptError
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the onScriptError method properly sets the on_script_error option
     * What is being tested:
     * - The onScriptError method of the Numeric class (inherited by IntegerType)
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
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::script
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the script method properly sets the script option
     * What is being tested:
     * - The script method of the Numeric class (inherited by IntegerType)
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
        $script = 'ctx._source.integer_field = params.value';
        
        // Act
        $result = $this->type->script($script);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($script, $options['script'], 'script option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::store
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the store method properly sets the store option
     * What is being tested:
     * - The store method of the Numeric class (inherited by IntegerType)
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
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::timeSeriesDimension
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the timeSeriesDimension method properly sets the time_series_dimension option
     * What is being tested:
     * - The timeSeriesDimension method of the Numeric class (inherited by IntegerType)
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
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::timeSeriesMetric
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the timeSeriesMetric method properly sets the time_series_metric option
     * What is being tested:
     * - The timeSeriesMetric method of the Numeric class (inherited by IntegerType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting time_series_metric to 'counter'
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The time_series_metric option should be set to 'counter'
     * @return void
     */
    public function testTimeSeriesMetric(): void
    {
        // Arrange
        $timeSeriesMetric = 'counter';
        
        // Act
        $result = $this->type->timeSeriesMetric($timeSeriesMetric);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($timeSeriesMetric, $options['time_series_metric'], 'time_series_metric option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the getOptions method returns all configured options
     * What is being tested:
     * - The getOptions method of the Numeric class (inherited by IntegerType) when multiple options are set
     * Conditions/Scenarios:
     * - Setting multiple options (coerce, doc_values, ignore_malformed, etc.)
     * Expected results:
     * - The getOptions method should return all configured options with their correct values
     * @return void
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {
        // Arrange
        $metaData = ['description' => 'Test integer field'];
        $script = 'ctx._source.integer_field = params.value';
        
        $this->type->coerce(true)
            ->docValues(false)
            ->ignoreMalformed(true)
            ->index(false)
            ->meta($metaData)
            ->nullValue(0)
            ->onScriptError('fail')
            ->script($script)
            ->store(true)
            ->timeSeriesDimension(true)
            ->timeSeriesMetric('counter');
        
        // Act
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertTrue($options['coerce'], 'coerce option should be set to true');
        $this->assertFalse($options['doc_values'], 'doc_values option should be set to false');
        $this->assertTrue($options['ignore_malformed'], 'ignore_malformed option should be set to true');
        $this->assertFalse($options['index'], 'index option should be set to false');
        $this->assertEquals($metaData, $options['meta'], 'meta option should be set to the provided array');
        $this->assertEquals(0, $options['null_value'], 'null_value option should be set to the provided integer');
        $this->assertEquals('fail', $options['on_script_error'], 'on_script_error option should be set to the provided value');
        $this->assertEquals($script, $options['script'], 'script option should be set to the provided value');
        $this->assertTrue($options['store'], 'store option should be set to true');
        $this->assertTrue($options['time_series_dimension'], 'time_series_dimension option should be set to true');
        $this->assertEquals('counter', $options['time_series_metric'], 'time_series_metric option should be set to the provided value');
    }
}
