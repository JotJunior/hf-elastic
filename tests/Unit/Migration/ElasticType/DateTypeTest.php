<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\DateType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

class DateTypeTest extends TestCase
{
    private DateType $type;
    
    protected function setUp(): void
    {
        $this->type = new DateType('test_field');
    }
    
    public function testConstructor(): void
    {
        $this->assertEquals('test_field', $this->type->getName());
        $this->assertEquals(Type::date, $this->type->getType());
    }
    
    public function testDocValues(): void
    {
        $result = $this->type->docValues(false);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertFalse($options['doc_values']);
    }
    
    public function testFormat(): void
    {
        $result = $this->type->format(true);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertTrue($options['format']);
    }
    
    public function testLocale(): void
    {
        $result = $this->type->locale(true);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertTrue($options['locale']);
    }
    
    public function testIgnoreMalformed(): void
    {
        $result = $this->type->ignoreMalformed(true);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertTrue($options['ignore_malformed']);
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
    
    public function testOnScriptError(): void
    {
        $result = $this->type->on_script_error(true);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertTrue($options['on_script_error']);
    }
    
    public function testScript(): void
    {
        $result = $this->type->script(true);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertTrue($options['script']);
    }
    
    public function testStore(): void
    {
        $result = $this->type->store(true);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertTrue($options['store']);
    }
    
    public function testMeta(): void
    {
        $result = $this->type->meta(true);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertTrue($options['meta']);
    }
}
