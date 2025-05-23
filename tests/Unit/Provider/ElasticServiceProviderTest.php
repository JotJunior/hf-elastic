<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\Unit\Provider;

use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\ClientBuilder;
use Jot\HfElastic\Contracts\ClientFactoryInterface;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Query\ElasticQueryBuilder;
use Jot\HfElastic\Query\OperatorRegistry;
use Jot\HfElastic\Services\IndexNameFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @internal
 * @coversNothing
 */
class ElasticServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        // This test class is skipped because ElasticServiceProvider no longer exists
        $this->markTestSkipped('ElasticServiceProvider class no longer exists in the project. It has been replaced by ConfigProvider.');
    }

    /**
     * This test class is skipped because ElasticServiceProvider no longer exists.
     * It has been replaced by ConfigProvider.
     */
    public function testRegister(): void
    {
        $this->markTestSkipped('ElasticServiceProvider class no longer exists in the project. It has been replaced by ConfigProvider.');

        // Arrange
        $defineCalls = [];

        // Configurar o mock do container para capturar as chamadas a define()
        $this->container->method('define')
            ->willReturnCallback(function ($interface, $implementation) use (&$defineCalls) {
                $defineCalls[] = [$interface, $implementation];
                return null;
            });

        // Configurar o mock do container para retornar o config
        $this->container->method('get')
            ->willReturnCallback(function ($class) {
                if ($class === ConfigInterface::class) {
                    return $this->config;
                }
                return null;
            });

        // Configurar o mock do config para retornar valores específicos
        $this->config->method('get')
            ->willReturnCallback(function ($key, $default = null) {
                if ($key === 'hf_elastic.prefix') {
                    return 'test_prefix';
                }
                return $default;
            });

        // Act
        $this->provider->register($this->container);

        // Assert
        $this->assertCount(6, $defineCalls, 'O método define deve ser chamado 6 vezes');

        // Verificamos se as interfaces esperadas foram registradas
        $this->assertEquals(ClientFactoryInterface::class, $defineCalls[0][0]);
        $this->assertEquals(ClientBuilder::class, $defineCalls[0][1]);

        $this->assertEquals(QueryBuilderInterface::class, $defineCalls[1][0]);
        $this->assertEquals(ElasticQueryBuilder::class, $defineCalls[1][1]);
    }

    /**
     * Tests the operator registry callback creates and registers operators.
     */
    public function testOperatorRegistryCallback(): void
    {
        $this->markTestSkipped('ElasticServiceProvider class no longer exists in the project. It has been replaced by ConfigProvider.');

        // Arrange
        $registryCallback = null;

        // Verificamos especificamente a chamada para OperatorRegistry
        $this->container->method('define')
            ->willReturnCallback(function ($interface, $implementation) use (&$registryCallback) {
                if ($interface === OperatorRegistry::class) {
                    $registryCallback = $implementation;
                }
            });

        // Act
        $this->provider->register($this->container);

        // Verificamos se o callback foi capturado
        $this->assertNotNull($registryCallback, 'O callback do OperatorRegistry não foi registrado');

        // Executamos o callback e verificamos o resultado
        $registry = $registryCallback();
        $this->assertInstanceOf(OperatorRegistry::class, $registry);
    }

    /**
     * Tests the index name formatter callback creates formatter with prefix.
     */
    public function testIndexNameFormatterCallback(): void
    {
        $this->markTestSkipped('ElasticServiceProvider class no longer exists in the project. It has been replaced by ConfigProvider.');

        // Arrange
        $formatterCallback = null;

        // Verificamos especificamente a chamada para IndexNameFormatter
        $this->container->method('define')
            ->willReturnCallback(function ($interface, $implementation) use (&$formatterCallback) {
                if ($interface === IndexNameFormatter::class) {
                    $formatterCallback = $implementation;
                }
            });

        $this->container->expects($this->once())
            ->method('get')
            ->with(ConfigInterface::class)
            ->willReturn($this->config);

        $this->config->expects($this->once())
            ->method('get')
            ->with('hf_elastic.prefix', '')
            ->willReturn('test_prefix');

        // Act
        $this->provider->register($this->container);

        // Verificamos se o callback foi capturado
        $this->assertNotNull($formatterCallback, 'O callback do IndexNameFormatter não foi registrado');

        // Executamos o callback e verificamos o resultado
        $formatter = $formatterCallback($this->container);
        $this->assertInstanceOf(IndexNameFormatter::class, $formatter);
    }
}
