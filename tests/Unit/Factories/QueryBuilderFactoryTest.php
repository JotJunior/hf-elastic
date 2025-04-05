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

use Hyperf\Support;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Factories\QueryBuilderFactory;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * @covers \Jot\HfElastic\Factories\QueryBuilderFactory
 * @group unit
 * @internal
 */
class QueryBuilderFactoryTest extends TestCase
{
    private QueryBuilderFactory $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new QueryBuilderFactory();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Factories\QueryBuilderFactory::create
     * @group unit
     * Test that QueryBuilderFactory has a create method with correct return type
     */
    public function testCreateMethodHasCorrectReturnType(): void
    {
        // Arrange & Act
        $reflectionMethod = new ReflectionMethod(QueryBuilderFactory::class, 'create');

        // Assert
        $this->assertEquals(
            QueryBuilderInterface::class,
            $reflectionMethod->getReturnType()->getName(),
            'The create method should return a QueryBuilderInterface'
        );
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Factories\QueryBuilderFactory::create
     * @group unit
     * Test that create method uses the make function from Hyperf Support
     */
    public function testCreateUsesHyperfMakeFunction(): void
    {
        // This test verifies that the implementation of the create method
        // contains a call to the make function with QueryBuilderInterface

        // Arrange
        $reflectionMethod = new ReflectionMethod(QueryBuilderFactory::class, 'create');
        $methodBody = file_get_contents(__DIR__ . '/../../../src/Factories/QueryBuilderFactory.php');

        // Assert
        $this->assertStringContainsString(
            'make(QueryBuilderInterface::class)',
            $methodBody,
            'The create method should use the make function to instantiate QueryBuilderInterface'
        );
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Factories\QueryBuilderFactory
     * @group unit
     * Test that QueryBuilderFactory is properly constructed
     */
    public function testQueryBuilderFactoryConstruction(): void
    {
        // Arrange & Act
        $factory = new QueryBuilderFactory();

        // Assert
        $this->assertInstanceOf(QueryBuilderFactory::class, $factory);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Factories\QueryBuilderFactory
     * @group unit
     * Test that QueryBuilderFactory follows the factory pattern
     */
    public function testQueryBuilderFactoryFollowsFactoryPattern(): void
    {
        // Arrange & Act
        $reflectionClass = new ReflectionClass(QueryBuilderFactory::class);

        // Assert
        $this->assertTrue(
            $reflectionClass->hasMethod('create'),
            'Factory should have a create method'
        );

        // Verifica se o construtor não tem parâmetros ou se não existe explicitamente
        $constructor = $reflectionClass->getConstructor();
        if ($constructor !== null) {
            $this->assertEmpty(
                $constructor->getParameters(),
                'Factory constructor should not have parameters'
            );
        } else {
            // Se o construtor não existe explicitamente, o PHP usa o construtor padrão sem parâmetros
            $this->assertTrue(true, 'Factory uses default constructor without parameters');
        }
    }
}
