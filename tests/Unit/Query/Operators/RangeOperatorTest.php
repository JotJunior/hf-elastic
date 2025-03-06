<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Query\Operators;

use Jot\HfElastic\Query\Operators\RangeOperator;
use PHPUnit\Framework\TestCase;

class RangeOperatorTest extends TestCase
{
    private RangeOperator $operator;

    protected function setUp(): void
    {
        $this->operator = new RangeOperator();
    }

    public function testSupports(): void
    {
        // Act & Assert
        $this->assertTrue($this->operator->supports('>'), 'Operator should support greater than');
        $this->assertTrue($this->operator->supports('<'), 'Operator should support less than');
        $this->assertTrue($this->operator->supports('>='), 'Operator should support greater than or equal');
        $this->assertTrue($this->operator->supports('<='), 'Operator should support less than or equal');
        $this->assertFalse($this->operator->supports('='), 'Operator should not support equals');
        $this->assertFalse($this->operator->supports('!='), 'Operator should not support not equals');
    }

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
     */
    private function setPrivateProperty($object, $propertyName, $value): void
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
