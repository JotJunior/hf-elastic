<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\Unit\Factories;

use Jot\HfElastic\Contracts\PropertyInterface;
use Jot\HfElastic\Exception\UnsupportedTypeException;
use Jot\HfElastic\Factories\FieldTypeFactory;
use Jot\HfElastic\Migration\ElasticType\AliasType;
use Jot\HfElastic\Migration\ElasticType\BooleanType;
use Jot\HfElastic\Migration\ElasticType\DateType;
use Jot\HfElastic\Migration\ElasticType\DenseVectorType;
use Jot\HfElastic\Migration\ElasticType\IntegerType;
use Jot\HfElastic\Migration\ElasticType\KeywordType;
use Jot\HfElastic\Migration\ElasticType\NestedType;
use Jot\HfElastic\Migration\ElasticType\ObjectType;
use Jot\HfElastic\Migration\ElasticType\ScaledFloatType;
use Jot\HfElastic\Migration\ElasticType\SearchAsYouType;
use Jot\HfElastic\Migration\ElasticType\TextType;
use Jot\HfElastic\Migration\ElasticType\Type;
use Jot\HfElastic\Migration\FieldInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Factories\FieldTypeFactory
 * @group unit
 * @internal
 */
class FieldTypeFactoryTest extends TestCase
{
    private FieldTypeFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FieldTypeFactory();
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Factories\FieldTypeFactory::create
     * @group unit
     *
     * Test that the factory creates the correct type instances for text types
     *
     * What is being tested:
     * - The factory creates the correct instances for text field types
     *
     * Conditions/Scenarios:
     * - Creating text and keyword field types
     *
     * Expected results:
     * - The factory should return instances of the correct classes
     */
    public function testCreateTextTypes(): void
    {
        // Arrange & Act
        $textType = $this->factory->create('text', 'description');
        $keywordType = $this->factory->create('keyword', 'tag');
        $searchAsYouType = $this->factory->create('search_as_you_type', 'product_name');

        // Assert
        $this->assertInstanceOf(TextType::class, $textType);
        $this->assertInstanceOf(KeywordType::class, $keywordType);
        $this->assertInstanceOf(SearchAsYouType::class, $searchAsYouType);
        $this->assertInstanceOf(FieldInterface::class, $textType);

        $this->assertEquals('description', $textType->getName());
        $this->assertEquals('tag', $keywordType->getName());
        $this->assertEquals('product_name', $searchAsYouType->getName());

        $this->assertEquals(Type::text, $textType->getType());
        $this->assertEquals(Type::keyword, $keywordType->getType());
        $this->assertEquals(Type::searchAsYouType, $searchAsYouType->getType());
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Factories\FieldTypeFactory::create
     * @group unit
     *
     * Test that the factory creates the correct type instances for numeric types
     *
     * What is being tested:
     * - The factory creates the correct instances for numeric field types
     *
     * Conditions/Scenarios:
     * - Creating integer and scaled float field types
     *
     * Expected results:
     * - The factory should return instances of the correct classes with correct parameters
     */
    public function testCreateNumericTypes(): void
    {
        // Arrange & Act
        $integerType = $this->factory->create('integer', 'count');
        $scaledFloatType = $this->factory->create('scaled_float', 'price', ['scaling_factor' => 100]);

        // Assert
        $this->assertInstanceOf(IntegerType::class, $integerType);
        $this->assertInstanceOf(ScaledFloatType::class, $scaledFloatType);
        $this->assertInstanceOf(FieldInterface::class, $integerType);
        $this->assertInstanceOf(FieldInterface::class, $scaledFloatType);

        $this->assertEquals('count', $integerType->getName());
        $this->assertEquals('price', $scaledFloatType->getName());

        $this->assertEquals(Type::integer, $integerType->getType());
        $this->assertEquals(Type::scaledFloat, $scaledFloatType->getType());

        // Verificar as opções do campo
        $options = $scaledFloatType->getOptions();
        $this->assertArrayHasKey('scaling_factor', $options);
        $this->assertEquals(100, $options['scaling_factor']);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Factories\FieldTypeFactory::create
     * @group unit
     *
     * Test that the factory creates the correct type instances for date and boolean types
     *
     * What is being tested:
     * - The factory creates the correct instances for date and boolean field types
     *
     * Conditions/Scenarios:
     * - Creating date and boolean field types
     *
     * Expected results:
     * - The factory should return instances of the correct classes
     */
    public function testCreateDateAndBooleanTypes(): void
    {
        // Arrange & Act
        $dateType = $this->factory->create('date', 'created_at');
        $booleanType = $this->factory->create('boolean', 'is_active');

        // Assert
        $this->assertInstanceOf(DateType::class, $dateType);
        $this->assertInstanceOf(BooleanType::class, $booleanType);
        $this->assertInstanceOf(FieldInterface::class, $dateType);
        $this->assertInstanceOf(FieldInterface::class, $booleanType);

        $this->assertEquals('created_at', $dateType->getName());
        $this->assertEquals('is_active', $booleanType->getName());

        $this->assertEquals(Type::date, $dateType->getType());
        $this->assertEquals(Type::boolean, $booleanType->getType());
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Factories\FieldTypeFactory::create
     * @group unit
     *
     * Test that the factory creates the correct type instances for complex types
     *
     * What is being tested:
     * - The factory creates the correct instances for object and nested field types
     *
     * Conditions/Scenarios:
     * - Creating object and nested field types
     *
     * Expected results:
     * - The factory should return instances of the correct classes
     */
    public function testCreateComplexTypes(): void
    {
        // Arrange & Act
        $objectType = $this->factory->create('object', 'metadata');
        $nestedType = $this->factory->create('nested', 'comments');

        // Assert
        $this->assertInstanceOf(ObjectType::class, $objectType);
        $this->assertInstanceOf(NestedType::class, $nestedType);
        $this->assertInstanceOf(PropertyInterface::class, $objectType);
        $this->assertInstanceOf(PropertyInterface::class, $nestedType);

        $this->assertEquals('metadata', $objectType->getName());
        $this->assertEquals('comments', $nestedType->getName());

        $this->assertEquals(Type::object, $objectType->getType());
        $this->assertEquals(Type::nested, $nestedType->getType());
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Factories\FieldTypeFactory::create
     * @group unit
     *
     * Test that the factory creates the correct type instances for specialized types
     *
     * What is being tested:
     * - The factory creates the correct instances for specialized field types
     *
     * Conditions/Scenarios:
     * - Creating alias and dense vector field types
     *
     * Expected results:
     * - The factory should return instances of the correct classes with correct parameters
     */
    public function testCreateSpecializedTypes(): void
    {
        // Arrange & Act
        $aliasType = $this->factory->create('alias', 'name_alias');
        $denseVectorType = $this->factory->create('dense_vector', 'embedding', ['dims' => 768]);

        // Assert
        $this->assertInstanceOf(AliasType::class, $aliasType);
        $this->assertInstanceOf(DenseVectorType::class, $denseVectorType);
        $this->assertInstanceOf(FieldInterface::class, $aliasType);
        $this->assertInstanceOf(FieldInterface::class, $denseVectorType);

        $this->assertEquals('name_alias', $aliasType->getName());
        $this->assertEquals('embedding', $denseVectorType->getName());

        $this->assertEquals(Type::alias, $aliasType->getType());
        $this->assertEquals(Type::denseVector, $denseVectorType->getType());

        // Verificar as opções do campo
        $options = $denseVectorType->getOptions();
        $this->assertArrayHasKey('dims', $options);
        $this->assertEquals(768, $options['dims']);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Factories\FieldTypeFactory::create
     * @group unit
     *
     * Test that the factory throws an exception for unknown types
     *
     * What is being tested:
     * - The factory throws an exception when an unknown type is requested
     *
     * Conditions/Scenarios:
     * - Requesting a non-existent field type
     *
     * Expected results:
     * - An InvalidArgumentException should be thrown
     */
    public function testCreateThrowsExceptionForUnknownType(): void
    {
        // Assert
        $this->expectException(UnsupportedTypeException::class);
        $this->expectExceptionMessage('Unsupported field type: unknown_type');

        // Act
        $this->factory->create('unknown_type', 'field_name');
    }
}
