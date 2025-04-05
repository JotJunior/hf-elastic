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

use Jot\HfElastic\Migration\ElasticType\BinaryType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\BinaryType
 * @group unit
 * @internal
 */
class BinaryTypeTest extends TestCase
{
    private BinaryType $type;

    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new BinaryType('binary_field');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\BinaryType::__construct
     * @group unit
     * Test that the constructor properly initializes the BinaryType object
     * What is being tested:
     * - The constructor of the BinaryType class
     * - The type property is set to Type::binary
     * - The options array is initialized with default values
     * Conditions/Scenarios:
     * - Creating a new BinaryType instance
     * Expected results:
     * - The type property should be set to Type::binary
     * - The options array should be initialized with default values
     */
    public function testConstructor(): void
    {
        // Act
        $type = new BinaryType('binary_field');
        $options = $type->getOptions();

        // Assert
        $this->assertEquals(Type::binary, $type->getType(), 'Type should be set to binary');
        $this->assertEquals('binary_field', $type->getName(), 'Field name should match the provided name');
        $this->assertIsArray($options, 'Options should be an array');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Migration\ElasticType\BinaryType::docValues
     * @covers \Jot\HfElastic\Migration\ElasticType\BinaryType::getOptions
     * @group unit
     * Test that the docValues method properly sets the doc_values option
     * What is being tested:
     * - The docValues method of the BinaryType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\BinaryType::getOptions
     * @covers \Jot\HfElastic\Migration\ElasticType\BinaryType::store
     * @group unit
     * Test that the store method properly sets the store option
     * What is being tested:
     * - The store method of the BinaryType class
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
     * @covers \Jot\HfElastic\Migration\ElasticType\BinaryType::getOptions
     * @group unit
     * Test that the getOptions method returns all configured options
     * What is being tested:
     * - The getOptions method of the BinaryType class when multiple options are set
     * Conditions/Scenarios:
     * - Setting multiple options (doc_values and store)
     * Expected results:
     * - The getOptions method should return all configured options with their correct values
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {
        // Arrange
        $this->type->docValues(true)
            ->store(true);

        // Act
        $options = $this->type->getOptions();

        // Assert
        $this->assertTrue($options['doc_values'], 'doc_values option should be set to true');
        $this->assertTrue($options['store'], 'store option should be set to true');
    }
}
