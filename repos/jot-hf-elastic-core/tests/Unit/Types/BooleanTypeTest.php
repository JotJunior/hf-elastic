<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Tests\Unit\Types;

use Jot\HfElasticCore\Types\BooleanType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElasticCore\Types\BooleanType
 */
class BooleanTypeTest extends TestCase
{
    private BooleanType $type;
    private string $fieldName = 'is_active';

    protected function setUp(): void
    {
        $this->type = new BooleanType($this->fieldName);
    }

    public function testConstructor(): void
    {
        $this->assertEquals($this->fieldName, $this->type->getName());
        $this->assertEquals('boolean', $this->type->getType());
        $this->assertTrue($this->type->isFilterable());
        $this->assertFalse($this->type->isSearchable());
        $this->assertFalse($this->type->isSortable());
    }

    public function testSetStore(): void
    {
        $result = $this->type->setStore(true);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals(true, $this->type->getProperty('store'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals(true, $mapping['store']);
    }

    public function testSetDocValues(): void
    {
        $result = $this->type->setDocValues(false);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals(false, $this->type->getProperty('doc_values'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals(false, $mapping['doc_values']);
    }

    public function testSetNullValue(): void
    {
        $result = $this->type->setNullValue(false);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals(false, $this->type->getProperty('null_value'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals(false, $mapping['null_value']);
    }

    public function testSetBoost(): void
    {
        $boost = 1.5;
        $result = $this->type->setBoost($boost);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals($boost, $this->type->getProperty('boost'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals($boost, $mapping['boost']);
    }

    public function testSetIndex(): void
    {
        $result = $this->type->setIndex(false);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals(false, $this->type->getProperty('index'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals(false, $mapping['index']);
    }

    public function testToMapping(): void
    {
        $this->type->setStore(true)
            ->setDocValues(false)
            ->setNullValue(true)
            ->setBoost(2.0)
            ->setIndex(true);

        $expected = [
            'type' => 'boolean',
            'store' => true,
            'doc_values' => false,
            'null_value' => true,
            'boost' => 2.0,
            'index' => true,
        ];

        $this->assertEquals($expected, $this->type->toMapping());
    }
}
