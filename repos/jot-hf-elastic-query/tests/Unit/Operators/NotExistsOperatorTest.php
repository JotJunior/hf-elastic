<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Tests\Unit\Operators;

use Jot\HfElasticQuery\Operators\NotExistsOperator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElasticQuery\Operators\NotExistsOperator
 */
class NotExistsOperatorTest extends TestCase
{
    private NotExistsOperator $operator;

    protected function setUp(): void
    {
        $this->operator = new NotExistsOperator();
    }

    public function testGetName(): void
    {
        $this->assertEquals('not exists', $this->operator->getName());
    }

    public function testApply(): void
    {
        $field = 'tags';
        $value = true; // O valor u00e9 ignorado pelo operador not exists

        $expected = [
            'bool' => [
                'must_not' => [
                    'exists' => [
                        'field' => $field,
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->operator->apply($field, $value));
    }

    public function testSupportsWithAnyValue(): void
    {
        // O operador not exists suporta qualquer valor, ju00e1 que o valor u00e9 ignorado
        $this->assertTrue($this->operator->supports(true));
        $this->assertTrue($this->operator->supports(false));
        $this->assertTrue($this->operator->supports('test'));
        $this->assertTrue($this->operator->supports(123));
        $this->assertTrue($this->operator->supports([]));
        $this->assertTrue($this->operator->supports(null));
    }
}
