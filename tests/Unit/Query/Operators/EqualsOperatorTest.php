<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Query\Operators;

use Jot\HfElastic\Query\Operators\EqualsOperator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Query\Operators\EqualsOperator
 * @group unit
 */
class EqualsOperatorTest extends TestCase
{
    private EqualsOperator $operator;

    protected function setUp(): void
    {
        $this->operator = new EqualsOperator();
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\EqualsOperator::supports
     * @group unit
     * Test that the equals operator correctly identifies supported operators
     * What is being tested:
     * - The supports method of the EqualsOperator class
     * Conditions/Scenarios:
     * - Testing with '=' operator which should be supported
     * - Testing with '!=' operator which should not be supported
     * - Testing with '>' operator which should not be supported
     * Expected results:
     * - Returns true for '=' operator
     * - Returns false for '!=' operator
     * - Returns false for '>' operator
     * @return void
     */
    public function testSupportsOperator(): void
    {
        // Arrange
        $equalsOperator = '=';
        $notEqualsOperator = '!=';
        $greaterThanOperator = '>';

        // Act & Assert
        $this->assertTrue($this->operator->supports($equalsOperator), 'Operator should support equals operator');
        $this->assertFalse($this->operator->supports($notEqualsOperator), 'Operator should not support not equals operator');
        $this->assertFalse($this->operator->supports($greaterThanOperator), 'Operator should not support greater than operator');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\EqualsOperator::apply
     * @group unit
     * Test that the equals operator correctly applies the term query
     * What is being tested:
     * - The apply method of the EqualsOperator class
     * Conditions/Scenarios:
     * - Applying an equals condition to a field with a string value
     * - Using the 'must' context for the query
     * Expected results:
     * - Returns an array with a 'term' key
     * - The 'term' array contains the field name as key
     * - The field value matches the provided value
     * @return void
     */
    public function testApply(): void
    {
        // Arrange
        $field = 'name';
        $value = 'test';
        $context = 'must';

        // Act
        $result = $this->operator->apply($field, $value, $context);

        // Assert
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertArrayHasKey('term', $result, 'Result should have a term clause');
        $this->assertArrayHasKey($field, $result['term'], 'Term clause should target the specified field');
        $this->assertEquals($value, $result['term'][$field], 'Term clause should have the specified value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\EqualsOperator::apply
     * @group unit
     * Test that the equals operator correctly handles different value types
     * What is being tested:
     * - The apply method of the EqualsOperator class with different value types
     * Conditions/Scenarios:
     * - Applying an equals condition with an integer value
     * - Applying an equals condition with a boolean value
     * Expected results:
     * - The term query correctly preserves the value type
     * @return void
     */
    public function testApplyWithDifferentValueTypes(): void
    {
        // Arrange
        $field = 'age';
        $intValue = 25;
        $boolField = 'active';
        $boolValue = true;
        $context = 'must';

        // Act
        $intResult = $this->operator->apply($field, $intValue, $context);
        $boolResult = $this->operator->apply($boolField, $boolValue, $context);

        // Assert
        $this->assertSame($intValue, $intResult['term'][$field], 'Term clause should preserve integer value type');
        $this->assertSame($boolValue, $boolResult['term'][$boolField], 'Term clause should preserve boolean value type');
    }
}
