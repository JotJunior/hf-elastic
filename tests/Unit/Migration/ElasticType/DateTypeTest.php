<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\DateType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\DateType
 * @group unit
 */
class DateTypeTest extends TestCase
{
    private DateType $type;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new DateType('test_field');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::__construct
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getName
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getType
     * @group unit
     * Test that the constructor properly initializes the DateType
     * What is being tested:
     * - The constructor of the DateType class
     * - The getName method returns the correct field name
     * - The getType method returns the correct type constant
     * Conditions/Scenarios:
     * - Creating a new DateType with a specific field name
     * Expected results:
     * - The field name should match the provided name
     * - The type should be set to Type::date
     * @return void
     */
    public function testConstructor(): void
    {
        // Arrange - already done in setUp
        
        // Act & Assert
        $this->assertEquals('test_field', $this->type->getName(), 'Field name should match the provided name');
        $this->assertEquals(Type::date, $this->type->getType(), 'Type should be set to Type::date');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::docValues
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getOptions
     * @group unit
     * Test that the docValues method properly sets the doc_values option
     * What is being tested:
     * - The docValues method of the DateType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::format
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getOptions
     * @group unit
     * Test that the format method properly sets the format option
     * What is being tested:
     * - The format method of the DateType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting format to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The format option should be set to true
     * @return void
     */
    public function testFormat(): void
    {
        // Arrange
        $formatValue = true;
        
        // Act
        $result = $this->type->format($formatValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['format'], 'format option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::locale
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getOptions
     * @group unit
     * Test that the locale method properly sets the locale option
     * What is being tested:
     * - The locale method of the DateType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting locale to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The locale option should be set to true
     * @return void
     */
    public function testLocale(): void
    {
        // Arrange
        $localeValue = true;
        
        // Act
        $result = $this->type->locale($localeValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['locale'], 'locale option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::ignoreMalformed
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getOptions
     * @group unit
     * Test that the ignoreMalformed method properly sets the ignore_malformed option
     * What is being tested:
     * - The ignoreMalformed method of the DateType class
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
        $ignoreMalformedValue = true;
        
        // Act
        $result = $this->type->ignoreMalformed($ignoreMalformedValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['ignore_malformed'], 'ignore_malformed option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::index
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getOptions
     * @group unit
     * Test that the index method properly sets the index option
     * What is being tested:
     * - The index method of the DateType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::nullValue
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getOptions
     * @group unit
     * Test that the nullValue method properly sets the null_value option
     * What is being tested:
     * - The nullValue method of the DateType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting null_value to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The null_value option should be set to true
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
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::onScriptError
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getOptions
     * @group unit
     * Test that the onScriptError method properly sets the onScriptError option
     * What is being tested:
     * - The onScriptError method of the DateType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting onScriptError to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The onScriptError option should be set to true
     * @return void
     */
    public function testOnScriptError(): void
    {
        // Arrange
        $onScriptErrorValue = true;
        
        // Act
        $result = $this->type->onScriptError($onScriptErrorValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['on_script_error'], 'onScriptError option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::script
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getOptions
     * @group unit
     * Test that the script method properly sets the script option
     * What is being tested:
     * - The script method of the DateType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting script to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The script option should be set to true
     * @return void
     */
    public function testScript(): void
    {
        // Arrange
        $scriptValue = true;
        
        // Act
        $result = $this->type->script($scriptValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['script'], 'script option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::store
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getOptions
     * @group unit
     * Test that the store method properly sets the store option
     * What is being tested:
     * - The store method of the DateType class
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
        $storeValue = true;
        
        // Act
        $result = $this->type->store($storeValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['store'], 'store option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::meta
     * @covers \Jot\HfElastic\Migration\ElasticType\DateType::getOptions
     * @group unit
     * Test that the meta method properly sets the meta option
     * What is being tested:
     * - The meta method of the DateType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting meta to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The meta option should be set to true
     * @return void
     */
    public function testMeta(): void
    {
        // Arrange
        $metaValue = true;
        
        // Act
        $result = $this->type->meta($metaValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['meta'], 'meta option should be set to true');
    }
}
