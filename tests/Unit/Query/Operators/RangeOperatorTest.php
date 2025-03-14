<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Query\Operators;

use Jot\HfElastic\Query\Operators\RangeOperator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Query\Operators\RangeOperator
 * @group unit
 */
class RangeOperatorTest extends TestCase
{
    private RangeOperator $operator;

    protected function setUp(): void
    {
        $this->operator = new RangeOperator();
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\RangeOperator::supports
     * @group unit
     * Test that the range operator correctly identifies supported operators
     * What is being tested:
     * - The supports method of the RangeOperator class
     * Conditions/Scenarios:
     * - Testing with '>', '<', '>=', '<=' operators which should be supported
     * - Testing with '=', '!=' operators which should not be supported
     * Expected results:
     * - Returns true for '>', '<', '>=', '<=' operators
     * - Returns false for '=', '!=' operators
     * @return void
     */
    public function testSupports(): void
    {
        // Arrange
        $greaterThan = '>';
        $lessThan = '<';
        $greaterThanOrEqual = '>=';
        $lessThanOrEqual = '<=';
        $equals = '=';
        $notEquals = '!=';
        
        // Act & Assert
        $this->assertTrue($this->operator->supports($greaterThan), 'Operator should support greater than');
        $this->assertTrue($this->operator->supports($lessThan), 'Operator should support less than');
        $this->assertTrue($this->operator->supports($greaterThanOrEqual), 'Operator should support greater than or equal');
        $this->assertTrue($this->operator->supports($lessThanOrEqual), 'Operator should support less than or equal');
        $this->assertFalse($this->operator->supports($equals), 'Operator should not support equals');
        $this->assertFalse($this->operator->supports($notEquals), 'Operator should not support not equals');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\RangeOperator::apply
     * @group unit
     * Test that the range operator correctly applies the greater than (>) query
     * What is being tested:
     * - The apply method of the RangeOperator class with '>' operator
     * Conditions/Scenarios:
     * - Applying a greater than condition to a numeric field
     * - Using the 'must' context for the query
     * Expected results:
     * - Returns an array with a 'range' key
     * - The 'range' array contains the field name as key
     * - The field object contains a 'gt' parameter with the specified value
     * @return void
     */
    public function testApplyWithGreaterThan(): void
    {
        // Arrange
        $field = 'price';
        $value = 100;
        $context = 'must';
        
        // Set the current operator
        $this->setPrivateProperty($this->operator, 'currentOperator', '>');

        // Act
        $result = $this->operator->apply($field, $value, $context);

        // Assert
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertArrayHasKey('range', $result, 'Result should have a range clause');
        $this->assertArrayHasKey($field, $result['range'], 'Range clause should target the specified field');
        $this->assertArrayHasKey('gt', $result['range'][$field], 'Range clause should have a gt parameter');
        $this->assertEquals($value, $result['range'][$field]['gt'], 'Gt parameter should have the specified value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\RangeOperator::apply
     * @group unit
     * Test that the range operator correctly applies the less than (<) query
     * What is being tested:
     * - The apply method of the RangeOperator class with '<' operator
     * Conditions/Scenarios:
     * - Applying a less than condition to a numeric field
     * - Using the 'must' context for the query
     * Expected results:
     * - Returns an array with a 'range' key
     * - The 'range' array contains the field name as key
     * - The field object contains a 'lt' parameter with the specified value
     * @return void
     */
    public function testApplyWithLessThan(): void
    {
        // Arrange
        $field = 'price';
        $value = 100;
        $context = 'must';
        
        // Set the current operator
        $this->setPrivateProperty($this->operator, 'currentOperator', '<');

        // Act
        $result = $this->operator->apply($field, $value, $context);

        // Assert
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertArrayHasKey('range', $result, 'Result should have a range clause');
        $this->assertArrayHasKey($field, $result['range'], 'Range clause should target the specified field');
        $this->assertArrayHasKey('lt', $result['range'][$field], 'Range clause should have a lt parameter');
        $this->assertEquals($value, $result['range'][$field]['lt'], 'Lt parameter should have the specified value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\RangeOperator::apply
     * @group unit
     * Test that the range operator correctly applies the greater than or equal (>=) query
     * What is being tested:
     * - The apply method of the RangeOperator class with '>=' operator
     * Conditions/Scenarios:
     * - Applying a greater than or equal condition to a numeric field
     * - Using the 'must' context for the query
     * Expected results:
     * - Returns an array with a 'range' key
     * - The 'range' array contains the field name as key
     * - The field object contains a 'gte' parameter with the specified value
     * @return void
     */
    public function testApplyWithGreaterThanOrEqual(): void
    {
        // Arrange
        $field = 'price';
        $value = 100;
        $context = 'must';
        
        // Set the current operator
        $this->setPrivateProperty($this->operator, 'currentOperator', '>=');

        // Act
        $result = $this->operator->apply($field, $value, $context);

        // Assert
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertArrayHasKey('range', $result, 'Result should have a range clause');
        $this->assertArrayHasKey($field, $result['range'], 'Range clause should target the specified field');
        $this->assertArrayHasKey('gte', $result['range'][$field], 'Range clause should have a gte parameter');
        $this->assertEquals($value, $result['range'][$field]['gte'], 'Gte parameter should have the specified value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\RangeOperator::apply
     * @group unit
     * Test that the range operator correctly applies the less than or equal (<=) query
     * What is being tested:
     * - The apply method of the RangeOperator class with '<=' operator
     * Conditions/Scenarios:
     * - Applying a less than or equal condition to a numeric field
     * - Using the 'must' context for the query
     * Expected results:
     * - Returns an array with a 'range' key
     * - The 'range' array contains the field name as key
     * - The field object contains a 'lte' parameter with the specified value
     * @return void
     */
    public function testApplyWithLessThanOrEqual(): void
    {
        // Arrange
        $field = 'price';
        $value = 100;
        $context = 'must';
        
        // Set the current operator
        $this->setPrivateProperty($this->operator, 'currentOperator', '<=');

        // Act
        $result = $this->operator->apply($field, $value, $context);

        // Assert
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertArrayHasKey('range', $result, 'Result should have a range clause');
        $this->assertArrayHasKey($field, $result['range'], 'Range clause should target the specified field');
        $this->assertArrayHasKey('lte', $result['range'][$field], 'Range clause should have a lte parameter');
        $this->assertEquals($value, $result['range'][$field]['lte'], 'Lte parameter should have the specified value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\RangeOperator::apply
     * @group unit
     * Test that the range operator correctly applies the between query
     * What is being tested:
     * - The apply method of the RangeOperator class with 'between' operator
     * Conditions/Scenarios:
     * - Applying a between condition to a numeric field with an array of two values
     * - Using the 'must' context for the query
     * Expected results:
     * - Returns an array with a 'range' key
     * - The 'range' array contains the field name as key
     * - The field object contains both 'gte' and 'lte' parameters with the specified values
     * @return void
     */
    public function testApplyWithBetween(): void
    {
        // Arrange
        $field = 'price';
        $value = [50, 100];
        $context = 'must';
        
        // Set the current operator
        $this->setPrivateProperty($this->operator, 'currentOperator', 'between');

        // Act
        $result = $this->operator->apply($field, $value, $context);

        // Assert
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertArrayHasKey('range', $result, 'Result should have a range clause');
        $this->assertArrayHasKey($field, $result['range'], 'Range clause should target the specified field');
        $this->assertArrayHasKey('gte', $result['range'][$field], 'Range clause should have a gte parameter');
        $this->assertArrayHasKey('lte', $result['range'][$field], 'Range clause should have a lte parameter');
        $this->assertEquals($value[0], $result['range'][$field]['gte'], 'Gte parameter should have the specified lower value');
        $this->assertEquals($value[1], $result['range'][$field]['lte'], 'Lte parameter should have the specified upper value');
    }

    /**
     * Helper method to set a private property on an object
     * @param object $object The object to modify
     * @param string $propertyName The name of the property to set
     * @param mixed $value The value to set
     * @return void
     */
    private function setPrivateProperty($object, $propertyName, $value): void
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
