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

use Jot\HfElastic\Migration\ElasticType\DoubleType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\DoubleType
 * @group unit
 * @internal
 */
class DoubleTypeTest extends TestCase
{
    private DoubleType $type;

    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new DoubleType('double_field');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\DoubleType::__construct
     * @group unit
     * Test that the constructor properly initializes the DoubleType object
     * What is being tested:
     * - The constructor of the DoubleType class
     * - The type property is set to Type::double
     * - The options array is initialized with default values
     * Conditions/Scenarios:
     * - Creating a new DoubleType instance
     * Expected results:
     * - The type property should be set to Type::double
     * - The options array should be initialized with default values
     */
    public function testConstructor(): void
    {
        // Act
        $type = new DoubleType('double_field');
        $options = $type->getOptions();

        // Assert
        $this->assertEquals(Type::double, $type->getType(), 'Type should be set to double');
        $this->assertEquals('double_field', $type->getName(), 'Field name should match the provided name');
        $this->assertIsArray($options, 'Options should be an array');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::coerce
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the coerce method properly sets the coerce option
     * What is being tested:
     * - The coerce method of the Numeric class (inherited by DoubleType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting coerce to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The coerce option should be set to true
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
     * - The docValues method of the Numeric class (inherited by DoubleType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting doc_values to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The doc_values option should be set to true
     */
    public function testDocValues(): void
    {
        // Arrange
        $docValuesEnabled = true;

        // Act
        $result = $this->type->docValues($docValuesEnabled);
        $options = $this->type->getOptions();

        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['doc_values'], 'doc_values option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::ignoreMalformed
     * @group unit
     * Test that the ignoreMalformed method properly sets the ignore_malformed option
     * What is being tested:
     * - The ignoreMalformed method of the Numeric class (inherited by DoubleType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting ignore_malformed to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The ignore_malformed option should be set to true
     */
    public function testIgnoreMalformed(): void
    {
        // Arrange
        $ignoreMalformedEnabled = true;

        // Act
        $result = $this->type->ignoreMalformed($ignoreMalformedEnabled);
        $options = $this->type->getOptions();

        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['ignore_malformed'], 'ignore_malformed option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::index
     * @group unit
     * Test that the index method properly sets the index option
     * What is being tested:
     * - The index method of the Numeric class (inherited by DoubleType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting index to false
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The index option should be set to false
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
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::meta
     * @group unit
     * Test that the meta method properly sets the meta option
     * What is being tested:
     * - The meta method of the Numeric class (inherited by DoubleType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting meta to an array of metadata
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The meta option should be set to the provided array
     */
    public function testMeta(): void
    {
        // Arrange
        $metaData = ['description' => 'Test double field'];

        // Act
        $result = $this->type->meta($metaData);
        $options = $this->type->getOptions();

        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($metaData, $options['meta'], 'meta option should be set to the provided array');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::nullValue
     * @group unit
     * Test that the nullValue method properly sets the null_value option
     * What is being tested:
     * - The nullValue method of the Numeric class (inherited by DoubleType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting null_value to a double value
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The null_value option should be set to the provided value
     */
    public function testNullValue(): void
    {
        // Arrange
        $nullValue = 0.0;

        // Act
        $result = $this->type->nullValue($nullValue);
        $options = $this->type->getOptions();

        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($nullValue, $options['null_value'], 'null_value option should be set to the provided value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::onScriptError
     * @group unit
     * Test that the onScriptError method properly sets the on_script_error option
     * What is being tested:
     * - The onScriptError method of the Numeric class (inherited by DoubleType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting on_script_error to 'continue'
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The on_script_error option should be set to the provided value
     */
    public function testOnScriptError(): void
    {
        // Arrange
        $onScriptErrorValue = 'continue';

        // Act
        $result = $this->type->onScriptError($onScriptErrorValue);
        $options = $this->type->getOptions();

        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($onScriptErrorValue, $options['on_script_error'], 'on_script_error option should be set to the provided value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::script
     * @group unit
     * Test that the script method properly sets the script option
     * What is being tested:
     * - The script method of the Numeric class (inherited by DoubleType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting script to a script value
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The script option should be set to the provided value
     */
    public function testScript(): void
    {
        // Arrange
        $scriptValue = 'doc.field * 2';

        // Act
        $result = $this->type->script($scriptValue);
        $options = $this->type->getOptions();

        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($scriptValue, $options['script'], 'script option should be set to the provided value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::store
     * @group unit
     * Test that the store method properly sets the store option
     * What is being tested:
     * - The store method of the Numeric class (inherited by DoubleType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting store to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The store option should be set to true
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
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::timeSeriesDimension
     * @group unit
     * Test that the timeSeriesDimension method properly sets the time_series_dimension option
     * What is being tested:
     * - The timeSeriesDimension method of the Numeric class (inherited by DoubleType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting time_series_dimension to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The time_series_dimension option should be set to true
     */
    public function testTimeSeriesDimension(): void
    {
        // Arrange
        $timeSeriesDimensionEnabled = true;

        // Act
        $result = $this->type->timeSeriesDimension($timeSeriesDimensionEnabled);
        $options = $this->type->getOptions();

        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['time_series_dimension'], 'time_series_dimension option should be set to true');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::timeSeriesMetric
     * @group unit
     * Test that the timeSeriesMetric method properly sets the time_series_metric option
     * What is being tested:
     * - The timeSeriesMetric method of the Numeric class (inherited by DoubleType)
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting time_series_metric to 'counter'
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The time_series_metric option should be set to the provided value
     */
    public function testTimeSeriesMetric(): void
    {
        // Arrange
        $timeSeriesMetricValue = 'counter';

        // Act
        $result = $this->type->timeSeriesMetric($timeSeriesMetricValue);
        $options = $this->type->getOptions();

        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($timeSeriesMetricValue, $options['time_series_metric'], 'time_series_metric option should be set to the provided value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\Numeric::getOptions
     * @group unit
     * Test that the getOptions method returns all configured options
     * What is being tested:
     * - The getOptions method of the Numeric class (inherited by DoubleType) when multiple options are set
     * Conditions/Scenarios:
     * - Setting multiple options
     * Expected results:
     * - The getOptions method should return all configured options with their correct values
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {
        // Arrange
        $metaData = ['description' => 'Test double field'];

        $this->type->coerce(true)
            ->docValues(true)
            ->ignoreMalformed(true)
            ->index(false)
            ->meta($metaData)
            ->nullValue(0.0)
            ->onScriptError('continue')
            ->script('doc.field * 2')
            ->store(true)
            ->timeSeriesDimension(true)
            ->timeSeriesMetric('counter');

        // Act
        $options = $this->type->getOptions();

        // Assert
        $this->assertTrue($options['coerce'], 'coerce option should be set to true');
        $this->assertTrue($options['doc_values'], 'doc_values option should be set to true');
        $this->assertTrue($options['ignore_malformed'], 'ignore_malformed option should be set to true');
        $this->assertFalse($options['index'], 'index option should be set to false');
        $this->assertEquals($metaData, $options['meta'], 'meta option should be set to the provided array');
        $this->assertEquals(0.0, $options['null_value'], 'null_value option should be set to the provided value');
        $this->assertEquals('continue', $options['on_script_error'], 'on_script_error option should be set to the provided value');
        $this->assertEquals('doc.field * 2', $options['script'], 'script option should be set to the provided value');
        $this->assertTrue($options['store'], 'store option should be set to true');
        $this->assertTrue($options['time_series_dimension'], 'time_series_dimension option should be set to true');
        $this->assertEquals('counter', $options['time_series_metric'], 'time_series_metric option should be set to the provided value');
    }
}
