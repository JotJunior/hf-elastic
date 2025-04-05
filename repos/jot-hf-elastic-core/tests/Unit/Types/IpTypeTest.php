<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Tests\Unit\Types;

use Jot\HfElasticCore\Types\IpType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElasticCore\Types\IpType
 */
class IpTypeTest extends TestCase
{
    private IpType $type;
    private string $fieldName = 'client_ip';

    protected function setUp(): void
    {
        $this->type = new IpType($this->fieldName);
    }

    public function testConstructor(): void
    {
        $this->assertEquals($this->fieldName, $this->type->getName());
        $this->assertEquals('ip', $this->type->getType());
        $this->assertTrue($this->type->isFilterable());
        $this->assertTrue($this->type->isSortable());
        $this->assertFalse($this->type->isSearchable());
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
        $nullValue = '127.0.0.1';
        $result = $this->type->setNullValue($nullValue);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals($nullValue, $this->type->getProperty('null_value'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals($nullValue, $mapping['null_value']);
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
            ->setNullValue('0.0.0.0')
            ->setBoost(2.0)
            ->setIndex(true)
            ->setIgnoreMalformed(true);

        $expected = [
            'type' => 'ip',
            'store' => true,
            'doc_values' => false,
            'null_value' => '0.0.0.0',
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
            ->setNullValue('::1')
            ->setBoost(1.0)
            ->setIndex(true)
            ->setIgnoreMalformed(false);
        
        $this->assertSame($this->type, $result);
    }
}
