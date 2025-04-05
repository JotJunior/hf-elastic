<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\DenseVectorType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Migration\ElasticType\DenseVectorType
 * @group unit
 * @internal
 */
class DenseVectorTypeTest extends TestCase
{
    /**
     * Testa o construtor da classe DenseVectorType.
     */
    public function testConstructor(): void
    {
        // Teste com dimensões definidas
        $type = new DenseVectorType('vector_field', 128);
        $this->assertEquals('vector_field', $type->getName());

        // Teste sem dimensões definidas
        $type = new DenseVectorType('vector_field');
        $this->assertEquals('vector_field', $type->getName());
    }

    /**
     * Testa o método similarity.
     */
    public function testSimilarity(): void
    {
        $type = new DenseVectorType('vector_field', 128);
        $result = $type->similarity('cosine');

        $this->assertSame($type, $result, 'O método deve retornar a instância para encadeamento');
        $this->assertEquals(
            ['dims' => 128, 'similarity' => 'cosine'],
            $type->getOptions(),
            'O método deve definir a opção similarity'
        );
    }

    /**
     * Testa o método dimensions.
     */
    public function testDimensions(): void
    {
        $type = new DenseVectorType('vector_field');
        $result = $type->dimensions(256);

        $this->assertSame($type, $result, 'O método deve retornar a instância para encadeamento');
        $this->assertEquals(
            ['dims' => 256],
            $type->getOptions(),
            'O método deve definir a opção dims'
        );
    }

    /**
     * Testa a configuração de múltiplas opções.
     */
    public function testGetOptionsWithMultipleOptionsSet(): void
    {
        $type = new DenseVectorType('vector_field');
        $type->dimensions(512)
            ->similarity('dot_product');

        $this->assertEquals(
            ['dims' => 512, 'similarity' => 'dot_product'],
            $type->getOptions(),
            'Deve retornar todas as opções configuradas'
        );
    }
}
