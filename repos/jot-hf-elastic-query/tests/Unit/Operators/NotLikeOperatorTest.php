<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Tests\Unit\Operators;

use Jot\HfElasticQuery\Operators\NotLikeOperator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElasticQuery\Operators\NotLikeOperator
 */
class NotLikeOperatorTest extends TestCase
{
    private NotLikeOperator $operator;

    protected function setUp(): void
    {
        $this->operator = new NotLikeOperator();
    }

    public function testGetName(): void
    {
        $this->assertEquals('not like', $this->operator->getName());
    }

    public function testApply(): void
    {
        $field = 'title';
        $value = 'test*';

        $expected = [
            'bool' => [
                'must_not' => [
                    'wildcard' => [
                        $field => [
                            'value' => $value,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->operator->apply($field, $value));
    }

    public function testSupportsWithValidValue(): void
    {
        $this->assertTrue($this->operator->supports('test*'));
    }

    public function testSupportsWithEmptyString(): void
    {
        $this->assertFalse($this->operator->supports(''));
    }

    public function testSupportsWithNonStringValue(): void
    {
        $this->assertFalse($this->operator->supports(123));
        $this->assertFalse($this->operator->supports(true));
        $this->assertFalse($this->operator->supports(['test']));
        $this->assertFalse($this->operator->supports(null));
    }
}
