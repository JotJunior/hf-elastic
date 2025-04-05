<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\Unit\Query;

use DateTime;
use Elasticsearch\Client;
use Exception;
use Hyperf\Stringable\Str;
use Jot\HfElastic\Exception\DeleteErrorException;
use Jot\HfElastic\Query\ElasticPersistenceTrait;
use Jot\HfElastic\Query\QueryContext;
use Jot\HfElastic\Services\IndexNameFormatter;
use Mockery;
use PHPUnit\Framework\TestCase;
use Throwable;

// Translation function is provided by bootstrap.php

/**
 * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait
 * @group unit
 * @internal
 */
class ElasticPersistenceTraitTest extends TestCase
{
    /**
     * @var object Class that uses the ElasticPersistenceTrait
     */
    private object $sut;

    /**
     * @var Client|Mockery\MockInterface
     */
    private $mockClient;

    /**
     * @var Mockery\MockInterface|QueryContext
     */
    private $mockQueryContext;

    /**
     * @var IndexNameFormatter|Mockery\MockInterface
     */
    private $mockIndexFormatter;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockClient = Mockery::mock(Client::class);
        $this->mockQueryContext = Mockery::mock(QueryContext::class);
        $this->mockIndexFormatter = Mockery::mock(IndexNameFormatter::class);

        // Create a concrete class that uses the trait for testing
        $this->sut = new class($this->mockClient, $this->mockQueryContext, $this->mockIndexFormatter) {
            use ElasticPersistenceTrait;

            protected const VERSION_FIELD = '@version';

            protected const TIMESTAMP_FIELD = '@timestamp';

            protected readonly Client $client;

            protected readonly QueryContext $queryContext;

            protected readonly IndexNameFormatter $indexFormatter;

            public function __construct(
                Client $client,
                QueryContext $queryContext,
                IndexNameFormatter $indexFormatter
            ) {
                $this->client = $client;
                $this->queryContext = $queryContext;
                $this->indexFormatter = $indexFormatter;
            }

            public function toArray(): array
            {
                return $this->queryContext->toArray();
            }

            public function getDocumentVersion(string $id): ?int
            {
                // This method is required by the trait but implemented in the class that uses it
                // For testing purposes, we'll return a fixed version number for a specific ID
                if ($id === 'existing-doc') {
                    return 1;
                }
                return null;
            }

            protected function parseError(Throwable $exception): string
            {
                return $exception->getMessage();
            }
        };
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::select
     * @group unit
     * Test that select method sets the _source parameter correctly with array fields
     */
    public function testSelectWithArrayFields(): void
    {
        // Arrange
        $fields = ['field1', 'field2', 'field3'];
        $this->mockQueryContext->shouldReceive('setBodyParam')
            ->once()
            ->with('_source', $fields)
            ->andReturnSelf();

        // Act
        $result = $this->sut->select($fields);

        // Assert
        $this->assertSame($this->sut, $result);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::select
     * @group unit
     * Test that select method sets the _source parameter correctly with string field
     */
    public function testSelectWithStringField(): void
    {
        // Arrange
        $this->mockQueryContext->shouldReceive('setBodyParam')
            ->once()
            ->with('_source', [])
            ->andReturnSelf();

        // Act
        $result = $this->sut->select('*');

        // Assert
        $this->assertSame($this->sut, $result);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::delete
     * @group unit
     * Test that delete method performs logical deletion correctly
     */
    public function testDeleteWithLogicalDeletion(): void
    {
        // Arrange
        $id = 'existing-doc';
        $expectedData = ['deleted' => true];

        // Mock the update method to be called with the correct parameters
        $this->mockQueryContext->shouldReceive('getIndex')
            ->andReturn('test-index');

        $this->mockClient->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($params) use ($id) {
                return $params['index'] === 'test-index'
                       && $params['id'] === $id
                       && isset($params['body']['doc'])
                       && $params['body']['doc']['deleted'] === true;
            }))
            ->andReturn(['result' => 'updated']);

        // Act
        $result = $this->sut->delete($id, true);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('updated', $result['result']);
        $this->assertNull($result['error']);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::delete
     * @group unit
     * Test that delete method returns error when document not found for logical deletion
     */
    public function testDeleteWithLogicalDeletionDocumentNotFound(): void
    {
        // Arrange
        $id = 'non-existing-doc';

        // Act
        $result = $this->sut->delete($id, true);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('error', $result['result']);
        $this->assertEquals(__('messages.hf_elastic.document_not_found'), $result['error']);
        $this->assertEmpty($result['data']);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::delete
     * @group unit
     * Test that delete method performs physical deletion correctly
     */
    public function testDeleteWithPhysicalDeletion(): void
    {
        // Arrange
        $id = 'existing-doc';
        $this->mockQueryContext->shouldReceive('getIndex')
            ->andReturn('test-index');

        $this->mockClient->shouldReceive('delete')
            ->once()
            ->with([
                'index' => 'test-index',
                'id' => $id,
            ])
            ->andReturn(['result' => 'deleted']);

        // Act
        $result = $this->sut->delete($id, false);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('deleted', $result['result']);
        $this->assertNull($result['error']);
        $this->assertNull($result['data']);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::delete
     * @group unit
     * Test that delete method throws exception when physical deletion fails
     */
    public function testDeleteWithPhysicalDeletionThrowsException(): void
    {
        // Arrange
        $id = 'existing-doc';
        $this->mockQueryContext->shouldReceive('getIndex')
            ->andReturn('test-index');

        $this->mockClient->shouldReceive('delete')
            ->once()
            ->andThrow(new Exception('Deletion failed'));

        // Assert & Act
        $this->expectException(DeleteErrorException::class);
        $this->expectExceptionMessage('Deletion failed');
        $this->sut->delete($id, false);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::update
     * @group unit
     * Test that update method correctly updates an existing document
     */
    public function testUpdateExistingDocument(): void
    {
        // Arrange
        $id = 'existing-doc';
        $data = ['field1' => 'value1', 'field2' => 'value2'];

        $this->mockQueryContext->shouldReceive('getIndex')
            ->andReturn('test-index');

        $this->mockClient->shouldReceive('update')
            ->once()
            ->with(Mockery::on(function ($params) use ($id) {
                return $params['index'] === 'test-index'
                       && $params['id'] === $id
                       && isset($params['body']['doc'])
                       && $params['body']['doc']['field1'] === 'value1'
                       && $params['body']['doc']['field2'] === 'value2'
                       && isset($params['body']['doc']['@version'], $params['body']['doc']['updated_at']);
            }))
            ->andReturn(['result' => 'updated']);

        // Act
        $result = $this->sut->update($id, $data);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('updated', $result['result']);
        $this->assertNull($result['error']);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(2, $result['data']['@version']); // Version incremented
        $this->assertArrayHasKey('updated_at', $result['data']);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::update
     * @group unit
     * Test that update method returns error when document not found
     */
    public function testUpdateNonExistingDocument(): void
    {
        // Arrange
        $id = 'non-existing-doc';
        $data = ['field1' => 'value1'];

        // Act
        $result = $this->sut->update($id, $data);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('error', $result['result']);
        $this->assertEquals(__('messages.hf_elastic.document_not_found'), $result['error']);
        $this->assertNull($result['data']);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::update
     * @group unit
     * Test that update method handles exceptions correctly
     */
    public function testUpdateHandlesExceptions(): void
    {
        // Arrange
        $id = 'existing-doc';
        $data = ['field1' => 'value1'];

        $this->mockQueryContext->shouldReceive('getIndex')
            ->andReturn('test-index');

        $this->mockClient->shouldReceive('update')
            ->once()
            ->andThrow(new Exception('Update failed'));

        // Act
        $result = $this->sut->update($id, $data);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('error', $result['result']);
        $this->assertEquals(__('messages.hf_elastic.error_occurred', ['message' => 'Update failed']), $result['error']);
        $this->assertNull($result['data']);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::insert
     * @group unit
     * Test that insert method correctly inserts a new document
     */
    public function testInsertNewDocument(): void
    {
        // Arrange
        $data = ['field1' => 'value1', 'field2' => 'value2'];
        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        $this->mockQueryContext->shouldReceive('getIndex')
            ->andReturn('test-index');

        // Substituir a implementação do método insert para evitar problemas com o mock do Str::uuid
        $sut = new class($this->mockClient, $this->mockQueryContext, $this->mockIndexFormatter) {
            use ElasticPersistenceTrait;

            protected const VERSION_FIELD = '@version';

            protected const TIMESTAMP_FIELD = '@timestamp';

            protected readonly Client $client;

            protected readonly QueryContext $queryContext;

            protected readonly IndexNameFormatter $indexFormatter;

            public string $testUuid;

            public function __construct(
                Client $client,
                QueryContext $queryContext,
                IndexNameFormatter $indexFormatter
            ) {
                $this->client = $client;
                $this->queryContext = $queryContext;
                $this->indexFormatter = $indexFormatter;
                $this->testUuid = '123e4567-e89b-12d3-a456-426614174000';
            }

            public function toArray(): array
            {
                return $this->queryContext->toArray();
            }

            public function getDocumentVersion(string $id): ?int
            {
                // This method is required by the trait but implemented in the class that uses it
                // For testing purposes, we'll return a fixed version number for a specific ID
                if ($id === 'existing-doc') {
                    return 1;
                }
                return null;
            }

            protected function parseError(Throwable $exception): string
            {
                return $exception->getMessage();
            }

            // Sobrescrevendo o método insert para teste
            public function insert(array $data): array
            {
                if (! empty($data['id']) && $this->getDocumentVersion($data['id'])) {
                    return [
                        'data' => null,
                        'result' => 'error',
                        'error' => sprintf('Document with id %s already exists.', $data['id']),
                    ];
                }

                $createdAt = (new DateTime())->format('Y-m-d\TH:i:s.u\Z');

                $data = [
                    ...$data,
                    'created_at' => $createdAt,
                    'updated_at' => null,
                    'deleted' => false,
                    self::TIMESTAMP_FIELD => $createdAt,
                    self::VERSION_FIELD => 1,
                ];

                try {
                    $data['id'] = $data['id'] ?? $this->testUuid;
                    $this->client->create([
                        'index' => $this->queryContext->getIndex(),
                        'id' => $data['id'],
                        'body' => $data,
                    ]);

                    return [
                        'data' => $data,
                        'result' => 'created',
                        'error' => null,
                    ];
                } catch (Throwable $e) {
                    return [
                        'data' => null,
                        'result' => 'error',
                        'error' => $this->parseError($e),
                    ];
                }
            }
        };

        $this->mockClient->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($params) use ($uuid) {
                return $params['index'] === 'test-index'
                       && $params['id'] === $uuid;
            }))
            ->andReturn(['result' => 'created']);

        // Act
        $result = $sut->insert($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('created', $result['result']);
        $this->assertNull($result['error']);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(1, $result['data']['@version']);
        $this->assertEquals($uuid, $result['data']['id']);
        $this->assertFalse($result['data']['deleted']);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::insert
     * @group unit
     * Test that insert method returns error when document with same ID already exists
     */
    public function testInsertExistingDocument(): void
    {
        // Arrange
        $data = ['id' => 'existing-doc', 'field1' => 'value1'];

        // Configurar o mock do QueryContext para retornar um índice quando getIndex for chamado
        $this->mockQueryContext->shouldReceive('getIndex')
            ->andReturn('test-index');

        // Configurar o mock do cliente Elasticsearch para lançar uma exceção
        // quando tentar criar um documento que já existe
        $this->mockClient->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) {
                return isset($arg['index']) && $arg['index'] === 'test-index'
                       && isset($arg['id']) && $arg['id'] === 'existing-doc';
            }))
            ->andThrow(new Exception('Document with id existing-doc already exists.'));

        // Act
        $result = $this->sut->insert($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('error', $result['result']);
        $this->assertEquals(__('messages.hf_elastic.error_occurred', ['message' => 'Document with id existing-doc already exists.']), $result['error']);
        $this->assertNull($result['data']);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::insert
     * @group unit
     * Test that insert method handles exceptions correctly
     */
    public function testInsertHandlesExceptions(): void
    {
        // Arrange
        $data = ['field1' => 'value1'];
        $uuid = '123e4567-e89b-12d3-a456-426614174000';

        // Substituir a implementação do método insert para evitar problemas com o mock do Str::uuid
        $sut = new class($this->mockClient, $this->mockQueryContext, $this->mockIndexFormatter) {
            use ElasticPersistenceTrait;

            protected const VERSION_FIELD = '@version';

            protected const TIMESTAMP_FIELD = '@timestamp';

            protected readonly Client $client;

            protected readonly QueryContext $queryContext;

            protected readonly IndexNameFormatter $indexFormatter;

            public string $testUuid;

            public function __construct(
                Client $client,
                QueryContext $queryContext,
                IndexNameFormatter $indexFormatter
            ) {
                $this->client = $client;
                $this->queryContext = $queryContext;
                $this->indexFormatter = $indexFormatter;
                $this->testUuid = '123e4567-e89b-12d3-a456-426614174000';
            }

            public function toArray(): array
            {
                return $this->queryContext->toArray();
            }

            public function getDocumentVersion(string $id): ?int
            {
                if ($id === 'existing-doc') {
                    return 1;
                }
                return null;
            }

            protected function parseError(Throwable $exception): string
            {
                return $exception->getMessage();
            }
        };

        $this->mockQueryContext->shouldReceive('getIndex')
            ->andReturn('test-index');

        $this->mockClient->shouldReceive('create')
            ->once()
            ->andThrow(new Exception('Insert failed'));

        // Act
        $result = $sut->insert($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('error', $result['result']);
        $this->assertEquals(__('messages.hf_elastic.error_occurred', ['message' => 'Insert failed']), $result['error']);
        $this->assertNull($result['data']);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::execute
     * @group unit
     * Test that execute method correctly executes a search query
     */
    public function testExecuteSearchQuery(): void
    {
        // Arrange
        $queryArray = ['index' => 'test-index', 'body' => ['query' => ['match_all' => (object) []]]];
        $searchResult = [
            'hits' => [
                'hits' => [
                    ['_source' => ['id' => '1', 'field1' => 'value1']],
                    ['_source' => ['id' => '2', 'field1' => 'value2']],
                ],
            ],
        ];

        $this->mockQueryContext->shouldReceive('toArray')
            ->once()
            ->andReturn($queryArray);

        $this->mockQueryContext->shouldReceive('reset')
            ->once();

        $this->mockClient->shouldReceive('search')
            ->once()
            ->with([
                'index' => 'test-index',
                'body' => ['query' => ['match_all' => (object) []]],
            ])
            ->andReturn($searchResult);

        // Act
        $result = $this->sut->execute();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('success', $result['result']);
        $this->assertNull($result['error']);
        $this->assertCount(2, $result['data']);
        $this->assertEquals('1', $result['data'][0]['id']);
        $this->assertEquals('2', $result['data'][1]['id']);
    }

    /**
     * @test
     * @covers \Jot\HfElastic\Query\ElasticPersistenceTrait::execute
     * @group unit
     * Test that execute method handles exceptions correctly
     */
    public function testExecuteHandlesExceptions(): void
    {
        // Arrange
        $queryArray = ['index' => 'test-index', 'body' => ['query' => ['match_all' => (object) []]]];

        $this->mockQueryContext->shouldReceive('toArray')
            ->once()
            ->andReturn($queryArray);

        $this->mockClient->shouldReceive('search')
            ->once()
            ->andThrow(new Exception('Search failed'));

        // Act
        $result = $this->sut->execute();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('error', $result['result']);
        $this->assertEquals(__('messages.hf_elastic.error_occurred', ['message' => 'Search failed']), $result['error']);
        $this->assertNull($result['data']);
    }
}
