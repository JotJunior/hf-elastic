<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Tests\Unit\Types;

use Jot\HfElasticCore\Types\HistogramType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElasticCore\Types\HistogramType
 */
class HistogramTypeTest extends TestCase
{
    private HistogramType $type;
    private string $fieldName = 'price_histogram';

    protected function setUp(): void
    {
        $this->type = new HistogramType($this->fieldName);
    }

    public function testConstructor(): void
    {
        $this->assertEquals($this->fieldName, $this->type->getName());
        $this->assertEquals('histogram', $this->type->getType());
        $this->assertFalse($this->type->isFilterable());
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

    public function testSetIgnoreMalformed(): void
    {
        $result = $this->type->setIgnoreMalformed(true);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals(true, $this->type->getProperty('ignore_malformed'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals(true, $mapping['ignore_malformed']);
    }

    public function testToMapping(): void
    {
        $this->type->setStore(true)
            ->setDocValues(false)
            ->setBoost(2.0)
            ->setIndex(true)
            ->setIgnoreMalformed(true);

        $expected = [
            'type' => 'histogram',
            'store' => true,
            'doc_values' => false,
            'boost' => 2.0,
            'index' => true,
            'ignore_malformed' => true,
        ];

        $this->assertEquals($expected, $this->type->toMapping());
    }

    public function testChainability(): void
    {
        $result = $this->type
            ->setStore(true)
            ->setDocValues(true)
            ->setBoost(1.0)
            ->setIndex(true)
            ->setIgnoreMalformed(false);
        
        $this->assertSame($this->type, $result);
    }
}
