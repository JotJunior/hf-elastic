<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\SearchAsYouType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType
 * @group unit
 */
class SearchAsYouTypeTest extends TestCase
{
    private SearchAsYouType $type;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new SearchAsYouType('test_field');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::__construct
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getName
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getType
     * @group unit
     *
     * Test that the constructor properly initializes the SearchAsYouType
     *
     * What is being tested:
     * - The constructor of the SearchAsYouType class
     * - The getName method returns the correct field name
     * - The getType method returns the correct type constant
     *
     * Conditions/Scenarios:
     * - Creating a new SearchAsYouType with a specific field name
     *
     * Expected results:
     * - The field name should match the provided name
     * - The type should be set to Type::searchAsYouType
     *
     * @return void
     */
    public function testConstructor(): void
    {
        // Arrange - already done in setUp
        
        // Act & Assert
        $this->assertEquals('test_field', $this->type->getName(), 'Field name should match the provided name');
        $this->assertEquals(Type::searchAsYouType, $this->type->getType(), 'Type should be set to Type::searchAsYouType');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::analyzer
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getOptions
     * @group unit
     *
     * Test that the analyzer method properly sets the analyzer option
     *
     * What is being tested:
     * - The analyzer method of the SearchAsYouType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting analyzer to 'standard'
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The analyzer option should be set to 'standard'
     *
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
        $this->assertEquals($analyzerValue, $options['analyzer'], 'analyzer option should be set to \'standard\'');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::searchAnalyzer
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getOptions
     * @group unit
     *
     * Test that the searchAnalyzer method properly sets the search_analyzer option
     *
     * What is being tested:
     * - The searchAnalyzer method of the SearchAsYouType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting search_analyzer to 'standard'
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The search_analyzer option should be set to 'standard'
     *
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
        $this->assertEquals($searchAnalyzerValue, $options['search_analyzer'], 'search_analyzer option should be set to \'standard\'');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::searchQuoteAnalyzer
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getOptions
     * @group unit
     *
     * Test that the searchQuoteAnalyzer method properly sets the search_quote_analyzer option
     *
     * What is being tested:
     * - The searchQuoteAnalyzer method of the SearchAsYouType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting search_quote_analyzer to 'standard'
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The search_quote_analyzer option should be set to 'standard'
     *
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
        $this->assertEquals($searchQuoteAnalyzerValue, $options['search_quote_analyzer'], 'search_quote_analyzer option should be set to \'standard\'');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::maxShingleSize
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getOptions
     * @group unit
     *
     * Test that the maxShingleSize method properly sets the max_shingle_size option
     *
     * What is being tested:
     * - The maxShingleSize method of the SearchAsYouType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting max_shingle_size to 3
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The max_shingle_size option should be set to 3
     *
     * @return void
     */
    public function testMaxShingleSize(): void
    {
        // Arrange
        $maxShingleSizeValue = 3;
        
        // Act
        $result = $this->type->maxShingleSize($maxShingleSizeValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($maxShingleSizeValue, $options['max_shingle_size'], 'max_shingle_size option should be set to 3');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::index
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getOptions
     * @group unit
     *
     * Test that the index method properly sets the index option
     *
     * What is being tested:
     * - The index method of the SearchAsYouType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::norms
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getOptions
     * @group unit
     *
     * Test that the norms method properly sets the norms option
     *
     * What is being tested:
     * - The norms method of the SearchAsYouType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting norms to false
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The norms option should be set to false
     *
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
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::store
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getOptions
     * @group unit
     *
     * Test that the store method properly sets the store option
     *
     * What is being tested:
     * - The store method of the SearchAsYouType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::similarity
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getOptions
     * @group unit
     *
     * Test that the similarity method properly sets the similarity option
     *
     * What is being tested:
     * - The similarity method of the SearchAsYouType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting similarity to 'BM25'
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The similarity option should be set to 'BM25'
     *
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
        $this->assertEquals($similarityValue, $options['similarity'], 'similarity option should be set to \'BM25\'');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::termVector
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getOptions
     * @group unit
     *
     * Test that the termVector method properly sets the term_vector option
     *
     * What is being tested:
     * - The termVector method of the SearchAsYouType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting term_vector to 'with_positions_offsets'
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The term_vector option should be set to 'with_positions_offsets'
     *
     * @return void
     */
    public function testTermVector(): void
    {
        // Arrange
        $termVectorValue = 'with_positions_offsets';
        
        // Act
        $result = $this->type->termVector($termVectorValue);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($termVectorValue, $options['term_vector'], 'term_vector option should be set to \'with_positions_offsets\'');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::copyTo
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getOptions
     * @group unit
     *
     * Test that the copyTo method properly sets the copy_to option with a string value
     *
     * What is being tested:
     * - The copyTo method of the SearchAsYouType class when passed a string
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting copy_to to a single field name as string
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The copy_to option should be set to the provided field name
     *
     * @return void
     */
    public function testCopyToWithString(): void
    {
        // Arrange
        $copyToField = 'another_field';
        
        // Act
        $result = $this->type->copyTo($copyToField);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($copyToField, $options['copy_to'], 'copy_to option should be set to the provided field name');
    }
    
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::copyTo
     * @covers \Jot\HfElastic\Migration\ElasticType\SearchAsYouType::getOptions
     * @group unit
     *
     * Test that the copyTo method properly sets the copy_to option with an array value
     *
     * What is being tested:
     * - The copyTo method of the SearchAsYouType class when passed an array
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     *
     * Conditions/Scenarios:
     * - Setting copy_to to an array of field names
     *
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The copy_to option should be set to the provided array of field names
     *
     * @return void
     */
    public function testCopyToWithArray(): void
    {
        // Arrange
        $copyToFields = ['field1', 'field2'];
        
        // Act
        $result = $this->type->copyTo($copyToFields);
        $options = $this->type->getOptions();
        
        // Assert
        $this->assertSame($this->type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($copyToFields, $options['copy_to'], 'copy_to option should be set to the provided array of field names');
    }
}
