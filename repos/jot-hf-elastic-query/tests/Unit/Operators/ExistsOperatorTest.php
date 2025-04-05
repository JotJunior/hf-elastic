<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Tests\Unit\Operators;

use Jot\HfElasticQuery\Operators\ExistsOperator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElasticQuery\Operators\ExistsOperator
 */
class ExistsOperatorTest extends TestCase
{
    private ExistsOperator $operator;

    protected function setUp(): void
    {
        $this->operator = new ExistsOperator();
    }

    public function testGetName(): void
    {
        $this->assertEquals('exists', $this->operator->getName());
    }

    public function testApply(): void
    {
        $field = 'tags';
        $value = true; // O valor é ignorado pelo operador exists

        $expected = [
            'exists' => [
                'field' => $field,
            ],
        ];

        $this->assertEquals($expected, $this->operator->apply($field, $value));
    }

    public function testSupportsWithAnyValue(): void
    {
        // O operador exists suporta qualquer valor, já que o valor é ignorado
        $this->assertTrue($this->operator->supports(true));
        $this->assertTrue($this->operator->supports(false));
        $this->assertTrue($this->operator->supports('test'));
        $this->assertTrue($this->operator->supports(123));
        $this->assertTrue($this->operator->supports([]));
        $this->assertTrue($this->operator->supports(null));
    }
}
