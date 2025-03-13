<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Tests\Unit\Operators;

use InvalidArgumentException;
use Jot\HfElasticQuery\Operators\BetweenOperator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElasticQuery\Operators\BetweenOperator
 */
class BetweenOperatorTest extends TestCase
{
    private BetweenOperator $operator;

    protected function setUp(): void
    {
        $this->operator = new BetweenOperator();
    }

    public function testGetName(): void
    {
        $this->assertEquals('between', $this->operator->getName());
    }

    public function testApply(): void
    {
        $field = 'price';
        $value = [10, 100];

        $expected = [
            'range' => [
                $field => [
                    'gte' => 10,
                    'lte' => 100,
                ],
            ],
        ];

        $this->assertEquals($expected, $this->operator->apply($field, $value));
    }

    public function testApplyWithInvalidValueThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Between operator requires an array with exactly two elements.');
        
        $this->operator->apply('price', [10]);
    }

    public function testSupportsWithValidValue(): void
    {
        $this->assertTrue($this->operator->supports([10, 100]));
    }

    public function testSupportsWithInvalidArraySize(): void
    {
        $this->assertFalse($this->operator->supports([10]));
        $this->assertFalse($this->operator->supports([10, 20, 30]));
    }

    public function testSupportsWithNonArrayValue(): void
    {
        $this->assertFalse($this->operator->supports('test'));
        $this->assertFalse($this->operator->supports(123));
        $this->assertFalse($this->operator->supports(true));
        $this->assertFalse($this->operator->supports(null));
    }
}
