<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\KeywordType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType
 * @group unit
 */
class KeywordTypeTest extends TestCase
{
    private KeywordType $type;
    
    protected function setUp(): void
    {
        $this->type = new KeywordType('test_field');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::__construct
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getName
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getType
     * @group unit
     * Test that the constructor properly initializes the KeywordType
     * What is being tested:
     * - The constructor of the KeywordType class
     * - The getName method returns the correct field name
     * - The getType method returns the correct type constant
     * Conditions/Scenarios:
     * - Creating a new KeywordType with a specific field name
     * Expected results:
     * - The field name should match the provided name
     * - The type should be set to Type::keyword
     * @return void
     */
    public function testConstructor(): void
    {
        // Arrange - already done in setUp
        
        // Act & Assert
        $this->assertEquals('test_field', $this->type->getName(), 'Field name should match the provided name');
        $this->assertEquals(Type::keyword, $this->type->getType(), 'Type should be set to Type::keyword');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::docValues
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the docValues method properly sets the doc_values option
     * What is being tested:
     * - The docValues method of the KeywordType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::eagerGlobalOrdinals
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the eagerGlobalOrdinals method properly sets the eager_global_ordinals option
     * What is being tested:
     * - The eagerGlobalOrdinals method of the KeywordType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting eager_global_ordinals to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The eager_global_ordinals option should be set to true
     * @return void
     */
    public function testEagerGlobalOrdinals(): void
    {
        // Arrange
        $eagerGlobalOrdinalsEnabled = true;
        
        // Act
        $result = $this->type->eagerGlobalOrdinals($eagerGlobalOrdinalsEnabled);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['eager_global_ordinals'], 'eager_global_ordinals option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::fields
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the fields method properly sets the fields option
     * What is being tested:
     * - The fields method of the KeywordType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting fields to an array of field definitions
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The fields option should be set to the provided array
     * @return void
     */
    public function testFields(): void
    {
        // Arrange
        $fields = ['raw' => ['type' => 'keyword']];
        
        // Act
        $result = $this->type->fields($fields);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($fields, $options['fields'], 'fields option should be set to the provided array');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::ignoreAbove
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the ignoreAbove method properly sets the ignore_above option
     * What is being tested:
     * - The ignoreAbove method of the KeywordType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting ignore_above to 256
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The ignore_above option should be set to 256
     * @return void
     */
    public function testIgnoreAbove(): void
    {
        // Arrange
        $ignoreAboveValue = 256;
        
        // Act
        $result = $this->type->ignoreAbove($ignoreAboveValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($ignoreAboveValue, $options['ignore_above'], 'ignore_above option should be set to 256');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::index
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the index method properly sets the index option
     * What is being tested:
     * - The index method of the KeywordType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::indexOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the indexOptions method properly sets the index_options option
     * What is being tested:
     * - The indexOptions method of the KeywordType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting index_options to 'docs'
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The index_options option should be set to 'docs'
     * @return void
     */
    public function testIndexOptions(): void
    {
        // Arrange
        $indexOptionsValue = 'docs';
        
        // Act
        $result = $this->type->indexOptions($indexOptionsValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($indexOptionsValue, $options['index_options'], 'index_options option should be set to "docs"');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::meta
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the meta method properly sets the meta option
     * What is being tested:
     * - The meta method of the KeywordType class
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
        $metaData = ['description' => 'Test keyword field'];
        
        // Act
        $result = $this->type->meta($metaData);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($metaData, $options['meta'], 'meta option should be set to the provided array');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::norms
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the norms method properly sets the norms option
     * What is being tested:
     * - The norms method of the KeywordType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting norms to false
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The norms option should be set to false
     * @return void
     */
    public function testNorms(): void
    {
        // Arrange
        $normsEnabled = false;
        
        // Act
        $result = $this->type->norms($normsEnabled);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertFalse($options['norms'], 'norms option should be set to false');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::nullValue
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the nullValue method properly sets the null_value option
     * What is being tested:
     * - The nullValue method of the KeywordType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting null_value to a string value
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The null_value option should be set to the provided string
     * @return void
     */
    public function testNullValue(): void
    {
        // Arrange
        $nullValue = 'N/A';
        
        // Act
        $result = $this->type->nullValue($nullValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($nullValue, $options['null_value'], 'null_value option should be set to the provided string');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::onScriptError
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the onScriptError method properly sets the on_script_error option
     * What is being tested:
     * - The onScriptError method of the KeywordType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::script
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the script method properly sets the script option
     * What is being tested:
     * - The script method of the KeywordType class
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
        $script = 'ctx._source.keyword_field = params.value';
        
        // Act
        $result = $this->type->script($script);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($script, $options['script'], 'script option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::store
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the store method properly sets the store option
     * What is being tested:
     * - The store method of the KeywordType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::similarity
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the similarity method properly sets the similarity option
     * What is being tested:
     * - The similarity method of the KeywordType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting similarity to 'BM25'
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The similarity option should be set to 'BM25'
     * @return void
     */
    public function testSimilarity(): void
    {
        // Arrange
        $similarityValue = 'BM25';
        
        // Act
        $result = $this->type->similarity($similarityValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($similarityValue, $options['similarity'], 'similarity option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::normalizer
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the normalizer method properly sets the normalizer option
     * What is being tested:
     * - The normalizer method of the KeywordType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting normalizer to 'lowercase'
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The normalizer option should be set to 'lowercase'
     * @return void
     */
    public function testNormalizer(): void
    {
        // Arrange
        $normalizerValue = 'lowercase';
        
        // Act
        $result = $this->type->normalizer($normalizerValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($normalizerValue, $options['normalizer'], 'normalizer option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::splitQueriesOnWhitespace
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the splitQueriesOnWhitespace method properly sets the split_queries_on_whitespace option
     * What is being tested:
     * - The splitQueriesOnWhitespace method of the KeywordType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting split_queries_on_whitespace to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The split_queries_on_whitespace option should be set to true
     * @return void
     */
    public function testSplitQueriesOnWhitespace(): void
    {
        // Arrange
        $splitQueriesOnWhitespaceEnabled = true;
        
        // Act
        $result = $this->type->splitQueriesOnWhitespace($splitQueriesOnWhitespaceEnabled);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['split_queries_on_whitespace'], 'split_queries_on_whitespace option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::timeSeriesDimension
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the timeSeriesDimension method properly sets the time_series_dimension option
     * What is being tested:
     * - The timeSeriesDimension method of the KeywordType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\KeywordType::getOptions
     * @group unit
     * Test that the getOptions method returns all configured options
     * What is being tested:
     * - The getOptions method of the KeywordType class when multiple options are set
     * Conditions/Scenarios:
     * - Setting multiple options (doc_values, eager_global_ordinals, fields, etc.)
     * Expected results:
     * - The getOptions method should return all configured options with their correct values
     * @return void
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {
        // Arrange
        $fields = ['raw' => ['type' => 'keyword']];
        $metaData = ['description' => 'Test keyword field'];
        $script = 'ctx._source.keyword_field = params.value';
        
        $this->type->docValues(false)
            ->eagerGlobalOrdinals(true)
            ->fields($fields)
            ->ignoreAbove(256)
            ->index(false)
            ->indexOptions('docs')
            ->meta($metaData)
            ->norms(false)
            ->nullValue('N/A')
            ->onScriptError('fail')
            ->script($script)
            ->store(true)
            ->similarity('BM25')
            ->normalizer('lowercase')
            ->splitQueriesOnWhitespace(true)
            ->timeSeriesDimension(true);
        
        // Act
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertFalse($options['doc_values'], 'doc_values option should be set to false');
        $this->assertTrue($options['eager_global_ordinals'], 'eager_global_ordinals option should be set to true');
        $this->assertEquals($fields, $options['fields'], 'fields option should be set to the provided array');
        $this->assertEquals(256, $options['ignore_above'], 'ignore_above option should be set to 256');
        $this->assertFalse($options['index'], 'index option should be set to false');
        $this->assertEquals('docs', $options['index_options'], 'index_options option should be set to "docs"');
        $this->assertEquals($metaData, $options['meta'], 'meta option should be set to the provided array');
        $this->assertFalse($options['norms'], 'norms option should be set to false');
        $this->assertEquals('N/A', $options['null_value'], 'null_value option should be set to the provided string');
        $this->assertEquals('fail', $options['on_script_error'], 'on_script_error option should be set to the provided value');
        $this->assertEquals($script, $options['script'], 'script option should be set to the provided value');
        $this->assertTrue($options['store'], 'store option should be set to true');
        $this->assertEquals('BM25', $options['similarity'], 'similarity option should be set to the provided value');
        $this->assertEquals('lowercase', $options['normalizer'], 'normalizer option should be set to the provided value');
        $this->assertTrue($options['split_queries_on_whitespace'], 'split_queries_on_whitespace option should be set to true');
        $this->assertTrue($options['time_series_dimension'], 'time_series_dimension option should be set to true');
    }
}
