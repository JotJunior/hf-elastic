<?php

namespace Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\SemanticTextType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\SemanticTextType
 * @group unit
 */
class SemanticTextTypeTest extends TestCase
{
    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SemanticTextType::__construct
     * @group unit
     * 
     * Test that the constructor properly initializes the SemanticTextType object
     * 
     * What is being tested:
     * - The constructor of the SemanticTextType class
     * - The type property is set to Type::semanticText
     * 
     * Conditions/Scenarios:
     * - Creating a new SemanticTextType instance
     * 
     * Expected results:
     * - The type property should be set to Type::semanticText
     * - The name property should be set to the provided name
     * - The options array should be initialized
     * 
     * @return void
     */
    public function testConstructor(): void
    {
        // Act
        $type = new SemanticTextType('semantic_text_field');
        $options = $type->getOptions();
        
        // Assert
        $this->assertEquals(Type::semanticText, $type->getType(), 'Type should be set to semanticText');
        $this->assertEquals('semantic_text_field', $type->getName(), 'Field name should match the provided name');
        $this->assertIsArray($options, 'Options should be an array');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SemanticTextType::inferenceId
     * @covers \Jot\HfElastic\Migration\ElasticType\SemanticTextType::getOptions
     * @group unit
     * 
     * Test that the inferenceId method properly sets the inference_id option
     * 
     * What is being tested:
     * - The inferenceId method of the SemanticTextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting inference_id to a string value
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The inference_id option should be set to the provided value
     * 
     * @return void
     */
    public function testInferenceId(): void
    {
        // Arrange
        $type = new SemanticTextType('semantic_text_field');
        $inferenceId = 'my_inference_model';
        
        // Act
        $result = $type->inferenceId($inferenceId);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($inferenceId, $options['inference_id'], 'inference_id option should be set to the provided value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SemanticTextType::searchInferenceId
     * @covers \Jot\HfElastic\Migration\ElasticType\SemanticTextType::getOptions
     * @group unit
     * 
     * Test that the searchInferenceId method properly sets the search_inference_id option
     * 
     * What is being tested:
     * - The searchInferenceId method of the SemanticTextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting search_inference_id to a string value
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The search_inference_id option should be set to the provided value
     * 
     * @return void
     */
    public function testSearchInferenceId(): void
    {
        // Arrange
        $type = new SemanticTextType('semantic_text_field');
        $searchInferenceId = 'my_search_inference_model';
        
        // Act
        $result = $type->searchInferenceId($searchInferenceId);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($searchInferenceId, $options['search_inference_id'], 'search_inference_id option should be set to the provided value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SemanticTextType::modelId
     * @covers \Jot\HfElastic\Migration\ElasticType\SemanticTextType::getOptions
     * @group unit
     * 
     * Test that the modelId method properly sets the model_id option
     * 
     * What is being tested:
     * - The modelId method of the SemanticTextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting model_id to a string value
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The model_id option should be set to the provided value
     * 
     * @return void
     */
    public function testModelId(): void
    {
        // Arrange
        $type = new SemanticTextType('semantic_text_field');
        $modelId = 'my_model';
        
        // Act
        $result = $type->modelId($modelId);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($modelId, $options['model_id'], 'model_id option should be set to the provided value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SemanticTextType::dimensions
     * @covers \Jot\HfElastic\Migration\ElasticType\SemanticTextType::getOptions
     * @group unit
     * 
     * Test that the dimensions method properly sets the dimensions option
     * 
     * What is being tested:
     * - The dimensions method of the SemanticTextType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * 
     * Conditions/Scenarios:
     * - Setting dimensions to an integer value
     * 
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The dimensions option should be set to the provided value
     * 
     * @return void
     */
    public function testDimensions(): void
    {
        // Arrange
        $type = new SemanticTextType('semantic_text_field');
        $dimensions = 768;
        
        // Act
        $result = $type->dimensions($dimensions);
        $options = $type->getOptions();
        
        // Assert
        $this->assertSame($type, $result, 'Method should return the same instance (fluent interface)');
        $this->assertEquals($dimensions, $options['dimensions'], 'dimensions option should be set to the provided value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\SemanticTextType::getOptions
     * @group unit
     * 
     * Test that the getOptions method returns all configured options
     * 
     * What is being tested:
     * - The getOptions method of the SemanticTextType class when multiple options are set
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
        $type = new SemanticTextType('semantic_text_field');
        $inferenceId = 'my_inference_model';
        $searchInferenceId = 'my_search_inference_model';
        $modelId = 'my_model';
        $dimensions = 768;
        
        // Act
        $type->inferenceId($inferenceId)
            ->searchInferenceId($searchInferenceId)
            ->modelId($modelId)
            ->dimensions($dimensions);
        
        $options = $type->getOptions();
        
        // Assert
        $this->assertEquals($inferenceId, $options['inference_id'], 'inference_id option should be set to the provided value');
        $this->assertEquals($searchInferenceId, $options['search_inference_id'], 'search_inference_id option should be set to the provided value');
        $this->assertEquals($modelId, $options['model_id'], 'model_id option should be set to the provided value');
        $this->assertEquals($dimensions, $options['dimensions'], 'dimensions option should be set to the provided value');
    }
}
