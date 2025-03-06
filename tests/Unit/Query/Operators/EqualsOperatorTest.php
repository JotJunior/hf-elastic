<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Query\Operators;

use Jot\HfElastic\Query\Operators\EqualsOperator;
use PHPUnit\Framework\TestCase;

class EqualsOperatorTest extends TestCase
{
    private EqualsOperator $operator;

    protected function setUp(): void
    {
        $this->operator = new EqualsOperator();
    }

    public function testSupportsOperator(): void
    {
        // Act & Assert
        $this->assertTrue($this->operator->supports('='), 'Operator should support equals operator');
        $this->assertFalse($this->operator->supports('!='), 'Operator should not support not equals operator');
        $this->assertFalse($this->operator->supports('>'), 'Operator should not support greater than operator');
    }

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
}
