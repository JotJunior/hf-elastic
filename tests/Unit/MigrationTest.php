<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\Unit;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use Exception;
use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\ClientBuilder;
use Jot\HfElastic\Migration;
use Jot\HfElastic\Migration\Mapping;
use Jot\HfElastic\Services\IndexNameFormatter;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @coversNothing
 */
class MigrationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testParseIndexName(): void
    {
        // Mock the IndexNameFormatter to return the expected value
        $container = Mockery::mock(ContainerInterface::class);
        $config = Mockery::mock(ConfigInterface::class);
        $client = Mockery::mock(Client::class);
        $clientBuilder = Mockery::mock(ClientBuilder::class);
        $indexNameFormatter = Mockery::mock(IndexNameFormatter::class);

        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn($config);
        $container->shouldReceive('get')->with(ClientBuilder::class)->andReturn($clientBuilder);
        $config->shouldReceive('get')->with('hf_elastic.prefix', '')->andReturn('test');
        $config->shouldReceive('get')->with('hf_elastic.settings', [])->andReturn([]);
        $clientBuilder->shouldReceive('build')->andReturn($client);

        // Create a custom Migration class for testing
        $migration = new class($container, $indexNameFormatter) extends Migration {
            public const INDEX_NAME = 'test_index';

            public bool $addPrefix = true;

            // Override constructor to inject mocked IndexNameFormatter
            public function __construct(ContainerInterface $container, IndexNameFormatter $formatter)
            {
                parent::__construct($container);
                // Replace the formatter with our mock
                $this->setIndexNameFormatter($formatter);
            }

            // Add setter for testing
            public function setIndexNameFormatter(IndexNameFormatter $formatter): void
            {
                $this->indexNameFormatter = $formatter;
            }

            public function up(): void
            {
            }

            public function down(): void
            {
            }
        };

        // Setup expectations for the formatter
        $indexNameFormatter->shouldReceive('format')
            ->with('test_index')
            ->andReturn('test_test_index');

        $this->assertEquals('test_test_index', $migration->parseIndexName('test_index'));

        // Test without prefix
        $migration->addPrefix = false;
        $this->assertEquals('test_index', $migration->parseIndexName('test_index'));
    }

    public function testCreate(): void
    {
        $container = Mockery::mock(ContainerInterface::class);
        $config = Mockery::mock(ConfigInterface::class);
        $client = Mockery::mock(Client::class);
        $indices = Mockery::mock(IndicesNamespace::class);
        $clientBuilder = Mockery::mock(ClientBuilder::class);
        $indexNameFormatter = Mockery::mock(IndexNameFormatter::class);

        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn($config);
        $container->shouldReceive('get')->with(ClientBuilder::class)->andReturn($clientBuilder);
        $config->shouldReceive('get')->with('hf_elastic.prefix', '')->andReturn('test');
        $config->shouldReceive('get')->with('hf_elastic.settings', [])->andReturn([]);
        $clientBuilder->shouldReceive('build')->andReturn($client);
        $client->shouldReceive('indices')->andReturn($indices);

        $mapping = Mockery::mock(Mapping::class);
        $mapping->shouldReceive('getName')->andReturn('test_index');
        $mapping->shouldReceive('setName')->with('test_index')->andReturnSelf();
        $mapping->shouldReceive('body')->andReturn(['index' => 'test_index', 'body' => []]);

        $indices->shouldReceive('exists')->with(['index' => 'test_index'])->andReturn(false);
        $indices->shouldReceive('create')->with(['index' => 'test_index', 'body' => []])->once();

        $migration = new class($container, $indexNameFormatter) extends Migration {
            public const INDEX_NAME = 'test_index';

            public bool $addPrefix = false;

            public function __construct(ContainerInterface $container, IndexNameFormatter $formatter)
            {
                parent::__construct($container);
                $this->setIndexNameFormatter($formatter);
            }

            public function setIndexNameFormatter(IndexNameFormatter $formatter): void
            {
                $this->indexNameFormatter = $formatter;
            }

            public function up(): void
            {
            }

            public function down(): void
            {
            }
        };

        // Setup expectations for the formatter
        $indexNameFormatter->shouldReceive('format')
            ->with('test_index')
            ->andReturn('test_index');

        // Execute the method and verify it doesn't throw an exception
        try {
            $migration->create($mapping);
            $this->assertTrue(true, 'Create method executed successfully');
        } catch (Exception $e) {
            $this->fail('Create method threw an exception: ' . $e->getMessage());
        }
    }

    public function testUpdate(): void
    {
        $container = Mockery::mock(ContainerInterface::class);
        $config = Mockery::mock(ConfigInterface::class);
        $client = Mockery::mock(Client::class);
        $indices = Mockery::mock(IndicesNamespace::class);
        $clientBuilder = Mockery::mock(ClientBuilder::class);
        $indexNameFormatter = Mockery::mock(IndexNameFormatter::class);

        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn($config);
        $container->shouldReceive('get')->with(ClientBuilder::class)->andReturn($clientBuilder);
        $config->shouldReceive('get')->with('hf_elastic.prefix', '')->andReturn('test');
        $config->shouldReceive('get')->with('hf_elastic.settings', [])->andReturn([]);
        $clientBuilder->shouldReceive('build')->andReturn($client);
        $client->shouldReceive('indices')->andReturn($indices);

        $mapping = Mockery::mock(Mapping::class);
        $mapping->shouldReceive('getName')->andReturn('test_index');
        $mapping->shouldReceive('setName')->with('test_index')->andReturnSelf();
        $mapping->shouldReceive('updateBody')->andReturn(['index' => 'test_index', 'body' => []]);

        $indices->shouldReceive('putMapping')->with(['index' => 'test_index', 'body' => []])->once();

        $migration = new class($container, $indexNameFormatter) extends Migration {
            public const INDEX_NAME = 'test_index';

            public bool $addPrefix = false;

            public function __construct(ContainerInterface $container, IndexNameFormatter $formatter)
            {
                parent::__construct($container);
                $this->setIndexNameFormatter($formatter);
            }

            public function setIndexNameFormatter(IndexNameFormatter $formatter): void
            {
                $this->indexNameFormatter = $formatter;
            }

            public function up(): void
            {
            }

            public function down(): void
            {
            }
        };

        // Setup expectations for the formatter
        $indexNameFormatter->shouldReceive('format')
            ->with('test_index')
            ->andReturn('test_index');

        // Execute the method and verify it doesn't throw an exception
        try {
            $migration->update($mapping);
            $this->assertTrue(true, 'Update method executed successfully');
        } catch (Exception $e) {
            $this->fail('Update method threw an exception: ' . $e->getMessage());
        }
    }

    public function testDelete(): void
    {
        $container = Mockery::mock(ContainerInterface::class);
        $config = Mockery::mock(ConfigInterface::class);
        $client = Mockery::mock(Client::class);
        $indices = Mockery::mock(IndicesNamespace::class);
        $clientBuilder = Mockery::mock(ClientBuilder::class);
        $indexNameFormatter = Mockery::mock(IndexNameFormatter::class);

        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn($config);
        $container->shouldReceive('get')->with(ClientBuilder::class)->andReturn($clientBuilder);
        $config->shouldReceive('get')->with('hf_elastic.prefix', '')->andReturn('test');
        $config->shouldReceive('get')->with('hf_elastic.settings', [])->andReturn([]);
        $clientBuilder->shouldReceive('build')->andReturn($client);
        $client->shouldReceive('indices')->andReturn($indices);

        $indices->shouldReceive('delete')->with(['index' => 'test_index'])->once();

        $migration = new class($container, $indexNameFormatter) extends Migration {
            public const INDEX_NAME = 'test_index';

            public bool $addPrefix = false;

            public function __construct(ContainerInterface $container, IndexNameFormatter $formatter)
            {
                parent::__construct($container);
                $this->setIndexNameFormatter($formatter);
            }

            public function setIndexNameFormatter(IndexNameFormatter $formatter): void
            {
                $this->indexNameFormatter = $formatter;
            }

            public function up(): void
            {
            }

            public function down(): void
            {
            }
        };

        // Setup expectations for the formatter
        $indexNameFormatter->shouldReceive('format')
            ->with('test_index')
            ->andReturn('test_index');

        // Execute the method and verify it doesn't throw an exception
        try {
            $migration->delete('test_index');
            $this->assertTrue(true, 'Delete method executed successfully');
        } catch (Exception $e) {
            $this->fail('Delete method threw an exception: ' . $e->getMessage());
        }
    }

    public function testExists(): void
    {
        $container = Mockery::mock(ContainerInterface::class);
        $config = Mockery::mock(ConfigInterface::class);
        $client = Mockery::mock(Client::class);
        $indices = Mockery::mock(IndicesNamespace::class);
        $clientBuilder = Mockery::mock(ClientBuilder::class);

        $container->shouldReceive('get')->with(ConfigInterface::class)->andReturn($config);
        $container->shouldReceive('get')->with(ClientBuilder::class)->andReturn($clientBuilder);
        $config->shouldReceive('get')->with('hf_elastic.prefix', '')->andReturn('test');
        $config->shouldReceive('get')->with('hf_elastic.settings', [])->andReturn([]);
        $clientBuilder->shouldReceive('build')->andReturn($client);
        $client->shouldReceive('indices')->andReturn($indices);

        $indices->shouldReceive('exists')->with(['index' => 'test_index'])->andReturn(true);

        $migration = new class($container) extends Migration {
            public const INDEX_NAME = 'test_index';

            public bool $addPrefix = false;

            public function up(): void
            {
            }

            public function down(): void
            {
            }
        };

        $this->assertTrue($migration->exists('test_index'));
    }
}
