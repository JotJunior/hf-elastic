<?php

namespace Jot\HfElastic\Tests\Unit\Migration;

use Jot\HfElastic\Migration\AbstractField;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

class AbstractFieldTest extends TestCase
{
    private AbstractField $field;
    
    protected function setUp(): void
    {
        $this->field = new class('test_field') extends AbstractField {
            public Type $type = Type::text;
        };
    }
    
    public function testConstructor(): void
    {
        $this->assertEquals('test_field', $this->field->getName());
    }
    
    public function testOptions(): void
    {
        $options = ['analyzer' => 'standard', 'index' => true];
        $result = $this->field->options($options);
        
        $this->assertSame($this->field, $result);
        $this->assertEquals($options, $this->field->getOptions());
    }
    
    public function testGetName(): void
    {
        $this->assertEquals('test_field', $this->field->getName());
    }
    
    public function testGetOptions(): void
    {
        $this->assertIsArray($this->field->getOptions());
        $this->assertEmpty($this->field->getOptions());
        
        $options = ['analyzer' => 'standard', 'index' => true];
        $this->field->options($options);
        $this->assertEquals($options, $this->field->getOptions());
    }
    
    public function testGetType(): void
    {
        $this->assertEquals(Type::text, $this->field->getType());
    }
    
    public function testOptionsFiltersNullValues(): void
    {
        $options = ['analyzer' => 'standard', 'index' => null, 'store' => true];
        $this->field->options($options);
        
        $expected = ['analyzer' => 'standard', 'store' => true];
        $this->assertEquals($expected, $this->field->getOptions());
    }
}
