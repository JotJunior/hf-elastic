<?php

declare(strict_types=1);

namespace Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\PointType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\PointType
 * @group unit
 */
class PointTypeTest extends TestCase
{
    /**
     * Testa o construtor da classe PointType
     */
    public function testConstructor(): void
    {        
        $type = new PointType('point_field');
        $this->assertEquals('point_field', $type->getName());
        $this->assertEquals(
            [],
            $type->getOptions()
        );
    }
    
    /**
     * Testa o método ignoreMalformed
     */
    public function testIgnoreMalformed(): void
    {        
        $type = new PointType('point_field');
        $result = $type->ignoreMalformed(true);
        
        $this->assertSame($type, $result, 'O método deve retornar a instância para encadeamento');
        $this->assertEquals(
            true, 
            $type->getOptions()['ignore_malformed'], 
            'O método deve definir a opção ignore_malformed'
        );
    }
    
    /**
     * Testa o método ignoreZValue
     */
    public function testIgnoreZValue(): void
    {        
        $type = new PointType('point_field');
        $result = $type->ignoreZValue(true);
        
        $this->assertSame($type, $result, 'O método deve retornar a instância para encadeamento');
        $this->assertEquals(
            true, 
            $type->getOptions()['ignore_z_value'], 
            'O método deve definir a opção ignore_z_value'
        );
    }
    
    /**
     * Testa o método nullValue
     */
    public function testNullValue(): void
    {        
        $type = new PointType('point_field');
        $result = $type->nullValue('0,0');
        
        $this->assertSame($type, $result, 'O método deve retornar a instância para encadeamento');
        $this->assertEquals(
            '0,0', 
            $type->getOptions()['null_value'], 
            'O método deve definir a opção null_value'
        );
    }
    
    /**
     * Testa a configuração de múltiplas opções
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {        
        $type = new PointType('point_field');
        $type->ignoreMalformed(true)
             ->ignoreZValue(true)
             ->nullValue('0,0');
        
        $options = $type->getOptions();
        $this->assertEquals(true, $options['ignore_malformed']);
        $this->assertEquals(true, $options['ignore_z_value']);
        $this->assertEquals('0,0', $options['null_value']);
    }
}
