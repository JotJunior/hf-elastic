<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\Unit\Query\Operators;

use Jot\HfElastic\Query\Operators\NotEqualsOperator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Query\Operators\NotEqualsOperator
 * @group unit
 * @internal
 */
class NotEqualsOperatorTest extends TestCase
{
    private NotEqualsOperator $operator;

    protected function setUp(): void
    {
        $this->operator = new NotEqualsOperator();
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\NotEqualsOperator::supports
     * @group unit
     * Test that the not equals operator correctly identifies supported operators
     * What is being tested:
     * - The supports method of the NotEqualsOperator class
     * Conditions/Scenarios:
     * - Testing with '!=' operator which should be supported
     * - Testing with '=' operator which should not be supported
     * - Testing with '>' operator which should not be supported
     * Expected results:
     * - Returns true for '!=' operator
     * - Returns false for '=' operator
     * - Returns false for '>' operator
     */
    public function testSupportsOperator(): void
    {
        // Arrange
        $notEqualsOperator = '!=';
        $equalsOperator = '=';
        $greaterThanOperator = '>';

        // Act & Assert
        $this->assertTrue($this->operator->supports($notEqualsOperator), 'Operator should support not equals operator');
        $this->assertFalse($this->operator->supports($equalsOperator), 'Operator should not support equals operator');
        $this->assertFalse($this->operator->supports($greaterThanOperator), 'Operator should not support greater than operator');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\NotEqualsOperator::apply
     * @group unit
     * Test that the not equals operator correctly applies the bool must_not term query
     * What is being tested:
     * - The apply method of the NotEqualsOperator class
     * Conditions/Scenarios:
     * - Applying a not equals condition to a field with a string value
     * - Using the 'must' context for the query
     * Expected results:
     * - Returns an array with a 'bool' key
     * - The 'bool' array contains a 'must_not' key with an array value
     * - The 'must_not' array contains a single term query
     * - The term query targets the specified field with the specified value
     */
    public function testApply(): void
    {
        // Arrange
        $field = 'status';
        $value = 'inactive';
        $context = 'must';

        // Act
        $result = $this->operator->apply($field, $value, $context);

        // Assert
        $this->assertIsArray($result, 'Result should be an array');
        $this->assertArrayHasKey('bool', $result, 'Result should have a bool clause');
        $this->assertArrayHasKey('must_not', $result['bool'], 'Bool clause should have a must_not clause');
        $this->assertCount(1, $result['bool']['must_not'], 'Must_not clause should have one condition');
        $this->assertArrayHasKey('term', $result['bool']['must_not'][0], 'Condition should be a term query');
        $this->assertArrayHasKey($field, $result['bool']['must_not'][0]['term'], 'Term query should target the specified field');
        $this->assertEquals($value, $result['bool']['must_not'][0]['term'][$field], 'Term query should have the specified value');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\NotEqualsOperator::apply
     * @group unit
     * Test that the not equals operator correctly handles different value types
     * What is being tested:
     * - The apply method of the NotEqualsOperator class with different value types
     * Conditions/Scenarios:
     * - Applying a not equals condition with an integer value
     * - Applying a not equals condition with a boolean value
     * Expected results:
     * - The term query correctly preserves the value type
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
        $this->assertSame($intValue, $intResult['bool']['must_not'][0]['term'][$field], 'Term query should preserve integer value type');
        $this->assertSame($boolValue, $boolResult['bool']['must_not'][0]['term'][$boolField], 'Term query should preserve boolean value type');
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\Operators\NotEqualsOperator::apply
     * @group unit
     * Test that the not equals operator correctly applies the bool must_not term query with different contexts
     * What is being tested:
     * - The apply method of the NotEqualsOperator class with different contexts
     * Conditions/Scenarios:
     * - Applying a not equals condition with a 'should' context
     * - Applying a not equals condition with a 'must_not' context
     * Expected results:
     * - The query structure is correct regardless of the context
     */
    public function testApplyWithDifferentContexts(): void
    {
        // Arrange
        $field = 'status';
        $value = 'inactive';
        $shouldContext = 'should';
        $mustNotContext = 'must_not';

        // Act
        $shouldResult = $this->operator->apply($field, $value, $shouldContext);
        $mustNotResult = $this->operator->apply($field, $value, $mustNotContext);

        // Assert
        // For 'should' context
        $this->assertIsArray($shouldResult, 'Result for should context should be an array');
        $this->assertArrayHasKey('bool', $shouldResult, 'Result for should context should have a bool clause');
        $this->assertArrayHasKey('must_not', $shouldResult['bool'], 'Bool clause for should context should have a must_not clause');
        $this->assertCount(1, $shouldResult['bool']['must_not'], 'Must_not clause for should context should have one condition');
        $this->assertArrayHasKey('term', $shouldResult['bool']['must_not'][0], 'Condition for should context should be a term query');
        $this->assertArrayHasKey($field, $shouldResult['bool']['must_not'][0]['term'], 'Term query for should context should target the specified field');
        $this->assertEquals($value, $shouldResult['bool']['must_not'][0]['term'][$field], 'Term query for should context should have the specified value');

        // For 'must_not' context
        $this->assertIsArray($mustNotResult, 'Result for must_not context should be an array');
        $this->assertArrayHasKey('bool', $mustNotResult, 'Result for must_not context should have a bool clause');
        $this->assertArrayHasKey('must_not', $mustNotResult['bool'], 'Bool clause for must_not context should have a must_not clause');
        $this->assertCount(1, $mustNotResult['bool']['must_not'], 'Must_not clause for must_not context should have one condition');
        $this->assertArrayHasKey('term', $mustNotResult['bool']['must_not'][0], 'Condition for must_not context should be a term query');
        $this->assertArrayHasKey($field, $mustNotResult['bool']['must_not'][0]['term'], 'Term query for must_not context should target the specified field');
        $this->assertEquals($value, $mustNotResult['bool']['must_not'][0]['term'][$field], 'Term query for must_not context should have the specified value');
    }
}
