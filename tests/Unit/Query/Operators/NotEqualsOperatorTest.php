<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Query\Operators;

use Jot\HfElastic\Query\Operators\NotEqualsOperator;
use PHPUnit\Framework\TestCase;

class NotEqualsOperatorTest extends TestCase
{
    private NotEqualsOperator $operator;

    protected function setUp(): void
    {
        $this->operator = new NotEqualsOperator();
    }

    public function testSupportsOperator(): void
    {
        // Act & Assert
        $this->assertTrue($this->operator->supports('!='), 'Operator should support not equals operator');
        $this->assertFalse($this->operator->supports('='), 'Operator should not support equals operator');
        $this->assertFalse($this->operator->supports('>'), 'Operator should not support greater than operator');
    }

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
}
