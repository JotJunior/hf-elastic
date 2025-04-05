<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\GeoPointType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\GeoPointType
 * @group unit
 * @internal
 */
class GeoPointTypeTest extends TestCase
{
    /**
     * Testa o construtor da classe GeoPointType.
     */
    public function testConstructor(): void
    {
        $type = new GeoPointType('location');
        $this->assertEquals('location', $type->getName());
        $this->assertEquals(
            [],
            $type->getOptions()
        );
    }

    /**
     * Testa o mu00e9todo ignoreMalformed.
     */
    public function testIgnoreMalformed(): void
    {
        $type = new GeoPointType('location');
        $result = $type->ignoreMalformed(true);

        $this->assertSame($type, $result, 'O mu00e9todo deve retornar a instu00e2ncia para encadeamento');
        $this->assertEquals(
            true,
            $type->getOptions()['ignore_malformed'],
            'O mu00e9todo deve definir a opu00e7u00e3o ignore_malformed'
        );
    }

    /**
     * Testa o mu00e9todo ignoreZValue.
     */
    public function testIgnoreZValue(): void
    {
        $type = new GeoPointType('location');
        $result = $type->ignoreZValue(true);

        $this->assertSame($type, $result, 'O mu00e9todo deve retornar a instu00e2ncia para encadeamento');
        $this->assertEquals(
            true,
            $type->getOptions()['ignore_z_value'],
            'O mu00e9todo deve definir a opu00e7u00e3o ignore_z_value'
        );
    }

    /**
     * Testa o mu00e9todo index.
     */
    public function testIndex(): void
    {
        $type = new GeoPointType('location');
        $result = $type->index(false);

        $this->assertSame($type, $result, 'O mu00e9todo deve retornar a instu00e2ncia para encadeamento');
        $this->assertEquals(
            false,
            $type->getOptions()['index'],
            'O mu00e9todo deve definir a opu00e7u00e3o index'
        );
    }

    /**
     * Testa o mu00e9todo nullValue.
     */
    public function testNullValue(): void
    {
        $type = new GeoPointType('location');
        $result = $type->nullValue('0,0');

        $this->assertSame($type, $result, 'O mu00e9todo deve retornar a instu00e2ncia para encadeamento');
        $this->assertEquals(
            '0,0',
            $type->getOptions()['null_value'],
            'O mu00e9todo deve definir a opu00e7u00e3o null_value'
        );
    }

    /**
     * Testa o mu00e9todo onScriptError.
     */
    public function testOnScriptError(): void
    {
        $type = new GeoPointType('location');
        $result = $type->onScriptError('continue');

        $this->assertSame($type, $result, 'O mu00e9todo deve retornar a instu00e2ncia para encadeamento');
        $this->assertEquals(
            'continue',
            $type->getOptions()['on_script_error'],
            'O mu00e9todo deve definir a opu00e7u00e3o on_script_error'
        );
    }

    /**
     * Testa o mu00e9todo script.
     */
    public function testScript(): void
    {
        $type = new GeoPointType('location');
        $result = $type->script('doc["field"].value');

        $this->assertSame($type, $result, 'O mu00e9todo deve retornar a instu00e2ncia para encadeamento');
        $this->assertEquals(
            'doc["field"].value',
            $type->getOptions()['script'],
            'O mu00e9todo deve definir a opu00e7u00e3o script'
        );
    }

    /**
     * Testa a configurau00e7u00e3o de mu00faltiplas opu00e7u00f5es.
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {
        $type = new GeoPointType('location');
        $type->ignoreMalformed(true)
            ->ignoreZValue(true)
            ->index(true)
            ->nullValue('0,0');

        $options = $type->getOptions();
        $this->assertEquals(true, $options['ignore_malformed']);
        $this->assertEquals(true, $options['ignore_z_value']);
        $this->assertEquals(true, $options['index']);
        $this->assertEquals('0,0', $options['null_value']);
    }
}
