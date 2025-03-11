<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\BooleanType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

class BooleanTypeTest extends TestCase
{
    private BooleanType $type;
    
    protected function setUp(): void
    {
        $this->type = new BooleanType('test_field');
    }
    
    public function testConstructor(): void
    {
        $this->assertEquals('test_field', $this->type->getName());
        $this->assertEquals(Type::boolean, $this->type->getType());
    }
    
    public function testBoost(): void
    {
        $result = $this->type->boost(1.5);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertEquals(1.5, $options['boost']);
    }
    
    public function testDocValues(): void
    {
        $result = $this->type->docValues(false);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertFalse($options['doc_values']);
    }
    
    public function testIndex(): void
    {
        $result = $this->type->index(false);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertFalse($options['index']);
    }
    
    public function testNullValue(): void
    {
        $result = $this->type->nullValue(true);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertTrue($options['null_value']);
    }
    
    public function testStore(): void
    {
        $result = $this->type->store(true);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertTrue($options['store']);
    }
}
