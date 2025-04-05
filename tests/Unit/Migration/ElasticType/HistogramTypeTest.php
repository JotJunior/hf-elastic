<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\HistogramType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\HistogramType
 * @group unit
 * @internal
 */
class HistogramTypeTest extends TestCase
{
    private HistogramType $type;

    protected function setUp(): void
    {
        $this->type = new HistogramType('test_field');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\HistogramType::__construct
     * @covers \Jot\HfElastic\Migration\ElasticType\HistogramType::getName
     * @covers \Jot\HfElastic\Migration\ElasticType\HistogramType::getType
     * @group unit
     * Test that the constructor properly initializes the HistogramType
     * What is being tested:
     * - The constructor of the HistogramType class
     * - The getName method returns the correct field name
     * - The getType method returns the correct type constant
     * Conditions/Scenarios:
     * - Creating a new HistogramType with a specific field name
     * Expected results:
     * - The field name should match the provided name
     * - The type should be set to Type::histogram
     */
    public function testConstructor(): void
    {
        // Arrange - already done in setUp

        // Act & Assert
        $this->assertEquals('test_field', $this->type->getName(), 'Field name should match the provided name');
        $this->assertEquals(Type::histogram, $this->type->getType(), 'Type should be set to Type::histogram');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\HistogramType::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\HistogramType::ignoreMalformed
     * @group unit
     * Test that the ignoreMalformed method properly sets the ignore_malformed option
     * What is being tested:
     * - The ignoreMalformed method of the HistogramType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\HistogramType::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\HistogramType::store
     * @group unit
     * Test that the store method properly sets the store option
     * What is being tested:
     * - The store method of the HistogramType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\HistogramType::docValues
     * @covers \Jot\HfElastic\Migration\ElasticType\HistogramType::getOptions
     * @group unit
     * Test that the docValues method properly sets the doc_values option
     * What is being tested:
     * - The docValues method of the HistogramType class
     * - The fluent interface pattern (method returns $this)
     * - The getOptions method returns the correct options
     * Conditions/Scenarios:
     * - Setting doc_values to false
     * Expected results:
     * - The method should return the same instance (fluent interface)
     * - The doc_values option should be set to false
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
     * @covers \Jot\HfElastic\Migration\ElasticType\HistogramType::getOptions
     * @group unit
     * Test that the getOptions method returns all configured options
     * What is being tested:
     * - The getOptions method of the HistogramType class when multiple options are set
     * Conditions/Scenarios:
     * - Setting multiple options (ignore_malformed, store, doc_values)
     * Expected results:
     * - The getOptions method should return all configured options with their correct values
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {
        // Arrange
        $this->type->ignoreMalformed(true)
            ->store(true)
            ->docValues(false);

        // Act
        $options = $this->type->getOptions();

        // Assert
        $this->assertTrue($options['ignore_malformed'], 'ignore_malformed option should be set to true');
        $this->assertTrue($options['store'], 'store option should be set to true');
        $this->assertFalse($options['doc_values'], 'doc_values option should be set to false');
    }
}
