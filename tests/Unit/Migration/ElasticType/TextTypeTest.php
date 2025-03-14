<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\TextType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\TextType
 * @group unit
 */
class TextTypeTest extends TestCase
{
    private TextType $type;
    
    protected function setUp(): void
    {
        $this->type = new TextType('test_field');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::__construct
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getName
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getType
     * @group unit
     * Test that the constructor properly initializes the TextType
     * What is being tested:
     * - The constructor of the TextType class
     * - The getName method returns the correct field name
     * - The getType method returns the correct type constant
     * Conditions/Scenarios:
     * - Creating a new TextType with a specific field name
     * Expected results:
     * - The field name should match the provided name
     * - The type should be set to Type::text
     * @return void
     */
    public function testConstructor(): void
    {
        // Arrange - already done in setUp
        
        // Act & Assert
        $this->assertEquals('test_field', $this->type->getName(), 'Field name should match the provided name');
        $this->assertEquals(Type::text, $this->type->getType(), 'Type should be set to Type::text');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::analyzer
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the analyzer method properly sets the analyzer option
     * What is being tested:
     * - The analyzer method of the TextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting analyzer to 'standard'
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The analyzer option should be set to 'standard'
     * @return void
     */
    public function testAnalyzer(): void
    {
        // Arrange
        $analyzerValue = 'standard';
        
        // Act
        $result = $this->type->analyzer($analyzerValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($analyzerValue, $options['analyzer'], 'analyzer option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::eagerGlobalOrdinals
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the eagerGlobalOrdinals method properly sets the eager_global_ordinals option
     * What is being tested:
     * - The eagerGlobalOrdinals method of the TextType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::fielddata
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the fielddata method properly sets the fielddata option
     * What is being tested:
     * - The fielddata method of the TextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting fielddata to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The fielddata option should be set to true
     * @return void
     */
    public function testFielddata(): void
    {
        // Arrange
        $fielddataEnabled = true;
        
        // Act
        $result = $this->type->fielddata($fielddataEnabled);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['fielddata'], 'fielddata option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::fielddataRequencyFilter
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the fielddataRequencyFilter method properly sets the fielddata_requency_filter option
     * What is being tested:
     * - The fielddataRequencyFilter method of the TextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting fielddata_requency_filter to an array of filter options
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The fielddata_requency_filter option should be set to the provided array
     * @return void
     */
    public function testFielddataRequencyFilter(): void
    {
        // Arrange
        $filter = ['min' => 0.001, 'max' => 0.1, 'min_segment_size' => 500];
        
        // Act
        $result = $this->type->fielddataRequencyFilter($filter);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($filter, $options['fielddata_requency_filter'], 'fielddata_requency_filter option should be set to the provided array');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::fields
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the fields method properly sets the fields option
     * What is being tested:
     * - The fields method of the TextType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::index
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the index method properly sets the index option
     * What is being tested:
     * - The index method of the TextType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::indexOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the indexOptions method properly sets the index_options option
     * What is being tested:
     * - The indexOptions method of the TextType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::indexPrefixes
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the indexPrefixes method properly sets the index_prefixes option
     * What is being tested:
     * - The indexPrefixes method of the TextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting index_prefixes to an array of prefix options
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The index_prefixes option should be set to the provided array
     * @return void
     */
    public function testIndexPrefixes(): void
    {
        // Arrange
        $prefixes = ['min_chars' => 1, 'max_chars' => 10];
        
        // Act
        $result = $this->type->indexPrefixes($prefixes);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($prefixes, $options['index_prefixes'], 'index_prefixes option should be set to the provided array');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::indexPhrases
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the indexPhrases method properly sets the index_phrases option
     * What is being tested:
     * - The indexPhrases method of the TextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting index_phrases to true
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The index_phrases option should be set to true
     * @return void
     */
    public function testIndexPhrases(): void
    {
        // Arrange
        $indexPhrasesEnabled = true;
        
        // Act
        $result = $this->type->indexPhrases($indexPhrasesEnabled);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertTrue($options['index_phrases'], 'index_phrases option should be set to true');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::norms
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the norms method properly sets the norms option
     * What is being tested:
     * - The norms method of the TextType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::positionIncrementGap
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the positionIncrementGap method properly sets the position_increment_gap option
     * What is being tested:
     * - The positionIncrementGap method of the TextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting position_increment_gap to 100
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The position_increment_gap option should be set to 100
     * @return void
     */
    public function testPositionIncrementGap(): void
    {
        // Arrange
        $positionIncrementGapValue = 100;
        
        // Act
        $result = $this->type->positionIncrementGap($positionIncrementGapValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($positionIncrementGapValue, $options['position_increment_gap'], 'position_increment_gap option should be set to 100');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::store
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the store method properly sets the store option
     * What is being tested:
     * - The store method of the TextType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::searchAnalyzer
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the searchAnalyzer method properly sets the search_analyzer option
     * What is being tested:
     * - The searchAnalyzer method of the TextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting search_analyzer to 'standard'
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The search_analyzer option should be set to 'standard'
     * @return void
     */
    public function testSearchAnalyzer(): void
    {
        // Arrange
        $searchAnalyzerValue = 'standard';
        
        // Act
        $result = $this->type->searchAnalyzer($searchAnalyzerValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($searchAnalyzerValue, $options['search_analyzer'], 'search_analyzer option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::searchQuoteAnalyzer
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the searchQuoteAnalyzer method properly sets the search_quote_analyzer option
     * What is being tested:
     * - The searchQuoteAnalyzer method of the TextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting search_quote_analyzer to 'standard'
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The search_quote_analyzer option should be set to 'standard'
     * @return void
     */
    public function testSearchQuoteAnalyzer(): void
    {
        // Arrange
        $searchQuoteAnalyzerValue = 'standard';
        
        // Act
        $result = $this->type->searchQuoteAnalyzer($searchQuoteAnalyzerValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($searchQuoteAnalyzerValue, $options['search_quote_analyzer'], 'search_quote_analyzer option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::similarity
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the similarity method properly sets the similarity option
     * What is being tested:
     * - The similarity method of the TextType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::termVector
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the termVector method properly sets the term_vector option
     * What is being tested:
     * - The termVector method of the TextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting term_vector to 'yes'
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The term_vector option should be set to 'yes'
     * @return void
     */
    public function testTermVector(): void
    {
        // Arrange
        $termVectorValue = 'yes';
        
        // Act
        $result = $this->type->termVector($termVectorValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($termVectorValue, $options['term_vector'], 'term_vector option should be set to the provided value');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::meta
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the meta method properly sets the meta option
     * What is being tested:
     * - The meta method of the TextType class
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
        $metaData = ['description' => 'Test text field'];
        
        // Act
        $result = $this->type->meta($metaData);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($metaData, $options['meta'], 'meta option should be set to the provided array');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\TextType::getOptions
     * @group unit
     * Test that the getOptions method returns all configured options
     * What is being tested:
     * - The getOptions method of the TextType class when multiple options are set
     * Conditions/Scenarios:
     * - Setting multiple options (analyzer, fielddata, index, etc.)
     * Expected results:
     * - The getOptions method should return all configured options with their correct values
     * @return void
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {
        // Arrange
        $analyzerValue = 'standard';
        $fields = ['raw' => ['type' => 'keyword']];
        $metaData = ['description' => 'Test text field'];
        $prefixes = ['min_chars' => 1, 'max_chars' => 10];
        
        $this->type->analyzer($analyzerValue)
            ->eagerGlobalOrdinals(true)
            ->fielddata(true)
            ->fields($fields)
            ->index(false)
            ->indexOptions('docs')
            ->indexPrefixes($prefixes)
            ->indexPhrases(true)
            ->norms(false)
            ->positionIncrementGap(100)
            ->store(true)
            ->searchAnalyzer('standard')
            ->searchQuoteAnalyzer('standard')
            ->similarity('BM25')
            ->termVector('yes')
            ->meta($metaData);
        
        // Act
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertEquals($analyzerValue, $options['analyzer'], 'analyzer option should be set to the provided value');
        $this->assertTrue($options['eager_global_ordinals'], 'eager_global_ordinals option should be set to true');
        $this->assertTrue($options['fielddata'], 'fielddata option should be set to true');
        $this->assertEquals($fields, $options['fields'], 'fields option should be set to the provided array');
        $this->assertFalse($options['index'], 'index option should be set to false');
        $this->assertEquals('docs', $options['index_options'], 'index_options option should be set to "docs"');
        $this->assertEquals($prefixes, $options['index_prefixes'], 'index_prefixes option should be set to the provided array');
        $this->assertTrue($options['index_phrases'], 'index_phrases option should be set to true');
        $this->assertFalse($options['norms'], 'norms option should be set to false');
        $this->assertEquals(100, $options['position_increment_gap'], 'position_increment_gap option should be set to 100');
        $this->assertTrue($options['store'], 'store option should be set to true');
        $this->assertEquals('standard', $options['search_analyzer'], 'search_analyzer option should be set to the provided value');
        $this->assertEquals('standard', $options['search_quote_analyzer'], 'search_quote_analyzer option should be set to the provided value');
        $this->assertEquals('BM25', $options['similarity'], 'similarity option should be set to the provided value');
        $this->assertEquals('yes', $options['term_vector'], 'term_vector option should be set to the provided value');
        $this->assertEquals($metaData, $options['meta'], 'meta option should be set to the provided array');
    }
}
