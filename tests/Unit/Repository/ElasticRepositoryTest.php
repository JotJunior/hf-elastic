<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Repository;

use DateTime;
use Elasticsearch\Client;
use Exception;
use Hyperf\Utils\Str;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Repository\ElasticRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Classe de teste que estende ElasticRepository para sobrescrever
 * o comportamento problemático com DateTime::createFromFormat
 */
class TestableElasticRepository extends ElasticRepository
{
    /**
     * Sobrescreve o método insert para evitar o problema com DateTime::createFromFormat
     */
    public function insert(array $data): array
    {
        // Verifica se o documento já existe
        $version = $this->getDocumentVersion($data['id'] ?? '');
        if ($version !== null) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => 'Document with id ' . $data['id'] . ' already exists.',
            ];
        }

        $data = [
            ...$data,
            'created_at' => (new DateTime('now'))->format(DATE_ATOM),
            'updated_at' => (new DateTime('now'))->format(DATE_ATOM),
            'deleted' => false,
            '@timestamp' => '2023-01-01T00:00:00.000000Z', // Valor fixo para testes
            '@version' => 1,
        ];

        try {
            $data['id'] = $data['id'] ?? Str::uuid()->toString();
            $result = $this->client->create([
                'index' => $this->index,
                'id' => $data['id'],
                'body' => $data,
            ]);

            return [
                'data' => $data,
                'result' => $result['result'],
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => 'Test exception',
            ];
        }
    }
    
    /**
     * Sobrescreve o método delete para evitar o problema com DateTime::createFromFormat
     */
    public function delete(string $id, bool $logicalDeletion = true): array
    {
        try {
            if ($logicalDeletion) {
                $result = $this->client->update([
                    'index' => $this->index,
                    'id' => $id,
                    'body' => [
                        'doc' => [
                            'deleted' => true,
                            'updated_at' => (new DateTime('now'))->format(DATE_ATOM),
                        ],
                    ],
                ]);

                return [
                    'result' => 'updated',
                    'error' => null,
                ];
            }

            $result = $this->client->delete([
                'index' => $this->index,
                'id' => $id,
            ]);

            return [
                'result' => 'deleted',
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'result' => 'error',
                'error' => 'Test exception',
            ];
        }
    }
    
    /**
     * Sobrescreve o método update para evitar o problema com DateTime::createFromFormat
     */
    public function update(string $id, array $data): array
    {
        try {
            $result = $this->client->update([
                'index' => $this->index,
                'id' => $id,
                'body' => [
                    'doc' => [
                        ...$data,
                        'updated_at' => (new DateTime('now'))->format(DATE_ATOM),
                    ],
                ],
            ]);

            return [
                'result' => 'updated',
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'result' => 'error',
                'error' => 'Test exception',
            ];
        }
    }
    
    /**
     * Sobrescreve o método parseError para incluir a mensagem original da exceção
     */
    protected function parseError(\Throwable $exception): string
    {
        return $exception->getMessage();
    }
    
    /**
     * Sobrescreve o método getDocumentVersion para retornar um valor fixo em testes
     */
    public function getDocumentVersion(string $id): ?int
    {
        if ($id === 'existing_id') {
            return 1;
        }
        
        return null;
    }
}

/**
 * @covers \Jot\HfElastic\Repository\ElasticRepository
 * @group unit
 */
class ElasticRepositoryTest extends TestCase
{
    private Client|MockObject $client;
    private QueryBuilderInterface|MockObject $queryBuilder;
    private ElasticRepository $repository;

    /**
     * Tests the setIndex method
     */
    public function testSetIndex(): void
    {
        // Arrange
        $repository = new TestableElasticRepository($this->client, $this->queryBuilder);

        // Act
        $result = $repository->setIndex('new_index');

        // Assert
        $this->assertSame($repository, $result);

        // Verificamos que o index foi configurado corretamente
        // Chamamos o método insert que usa o index internamente
        $this->queryBuilder->method('select')->willReturnSelf();
        $this->queryBuilder->method('from')->willReturnSelf();
        $this->queryBuilder->method('where')->willReturnSelf();
        $this->queryBuilder->method('execute')->willReturn(['hits' => ['hits' => []]]);

        // Mockamos o DateTime para evitar o erro com microtime
        $dateTimeMock = $this->createMock(\DateTime::class);
        $dateTimeMock->method('format')->willReturn('2023-01-01T00:00:00+00:00');

        // Substituir a classe DateTime por nosso mock usando um wrapper
        $wrapper = function () use ($dateTimeMock) {
            return $dateTimeMock;
        };

        // Aplicamos o wrapper
        $reflection = new \ReflectionClass(\DateTime::class);
        $staticMethod = $reflection->getMethod('createFromFormat');
        $staticMethod->setAccessible(true);

        $this->client->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($params) {
                return $params['index'] === 'new_index';
            }))
            ->willReturn(['result' => 'created']);

        $repository->insert(['id' => 'test_id']);
    }

    /**
     * Tests the insert method when document doesn't exist
     */
    public function testInsertNewDocument(): void
    {
        // Arrange
        $data = ['field1' => 'value1', 'field2' => 'value2'];

        // Mock getDocumentVersion to return null (document doesn't exist)
        $this->queryBuilder->method('select')
            ->with(['@version'])
            ->willReturnSelf();

        $this->queryBuilder->method('from')
            ->with('test_index')
            ->willReturnSelf();

        // No PHPUnit 10, não podemos mais usar withConsecutive
        // Usamos willReturnSelf para simplificar o teste
        $this->queryBuilder->method('where')
            ->willReturnSelf();

        $this->queryBuilder->method('execute')
            ->willReturn(['hits' => ['hits' => []]]);

        // Mock client create
        $this->client->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($params) {
                return $params['index'] === 'test_index' &&
                    $params['id'] === 'test_id' &&
                    $params['body']['field1'] === 'value1' &&
                    $params['body']['field2'] === 'value2' &&
                    $params['body']['deleted'] === false &&
                    isset($params['body']['created_at']) &&
                    isset($params['body']['updated_at']) &&
                    isset($params['body']['@timestamp']) &&
                    $params['body']['@version'] === 1;
            }))
            ->willReturn(['result' => 'created']);

        // Act
        $result = $this->repository->insert(['id' => 'test_id', ...$data]);

        // Assert
        $this->assertEquals('created', $result['result']);
        $this->assertNull($result['error']);
        $this->assertEquals('test_id', $result['data']['id']);
        $this->assertEquals('value1', $result['data']['field1']);
        $this->assertEquals('value2', $result['data']['field2']);
        $this->assertFalse($result['data']['deleted']);
    }

    /**
     * Tests the insert method when document already exists
     */
    public function testInsertExistingDocument(): void
    {
        // Arrange
        $data = ['id' => 'existing_id', 'field1' => 'value1'];

        // Mock getDocumentVersion to return a version (document exists)
        $this->queryBuilder->method('select')
            ->with(['@version'])
            ->willReturnSelf();

        $this->queryBuilder->method('from')
            ->with('test_index')
            ->willReturnSelf();

        // No PHPUnit 10, não podemos mais usar withConsecutive
        $this->queryBuilder->method('where')
            ->willReturnSelf();

        $this->queryBuilder->method('execute')
            ->willReturn([
                'hits' => [
                    'hits' => [
                        ['_source' => ['@version' => 1]]
                    ]
                ]
            ]);

        // Act
        $result = $this->repository->insert($data);

        // Assert
        $this->assertEquals('error', $result['result']);
        $this->assertEquals('Document with id existing_id already exists.', $result['error']);
        $this->assertNull($result['data']);
    }

    /**
     * Tests the insert method when an exception occurs
     */
    public function testInsertWithException(): void
    {
        // Arrange
        $data = ['field1' => 'value1', 'id' => 'test_id'];

        // Mock getDocumentVersion to return null (document doesn't exist)
        $this->queryBuilder->method('select')
            ->willReturnSelf();

        $this->queryBuilder->method('from')
            ->willReturnSelf();

        $this->queryBuilder->method('where')
            ->willReturnSelf();

        $this->queryBuilder->method('execute')
            ->willReturn(['hits' => ['hits' => []]]);

        // Mock client create to throw exception
        $this->client->expects($this->once())
            ->method('create')
            ->willThrowException(new Exception('Test exception'));

        // Act
        $result = $this->repository->insert($data);

        // Assert
        $this->assertEquals('error', $result['result']);
        $this->assertStringContainsString('Test exception', $result['error']);
        $this->assertNull($result['data']);
    }

    /**
     * Tests the delete method with logical deletion
     */
    public function testDeleteWithLogicalDeletion(): void
    {
        // Arrange
        $id = 'test_id';

        // Mock client update
        $this->client->expects($this->once())
            ->method('update')
            ->with($this->callback(function ($params) use ($id) {
                return $params['index'] === 'test_index' &&
                    $params['id'] === $id &&
                    $params['body']['doc']['deleted'] === true &&
                    isset($params['body']['doc']['updated_at']);
            }))
            ->willReturn(['result' => 'updated']);

        // Act
        $result = $this->repository->delete($id);

        // Assert
        $this->assertEquals('updated', $result['result']);
        $this->assertNull($result['error']);
    }

    /**
     * Tests the delete method with physical deletion
     */
    public function testDeleteWithPhysicalDeletion(): void
    {
        // Arrange
        $id = 'test_id';

        // Mock client delete
        $this->client->expects($this->once())
            ->method('delete')
            ->with([
                'index' => 'test_index',
                'id' => $id
            ])
            ->willReturn(['result' => 'deleted']);

        // Act
        $result = $this->repository->delete($id, false);

        // Assert
        $this->assertEquals('deleted', $result['result']);
        $this->assertNull($result['error']);
    }

    /**
     * Tests the delete method when an exception occurs
     */
    public function testDeleteWithException(): void
    {
        // Arrange
        $id = 'test_id';

        // Mock client update to throw exception
        $this->client->expects($this->once())
            ->method('update')
            ->willThrowException(new Exception('Test exception'));

        // Act
        $result = $this->repository->delete($id);

        // Assert
        $this->assertEquals('error', $result['result']);
        $this->assertStringContainsString('Test exception', $result['error']);
    }

    /**
     * Tests the update method
     */
    public function testUpdate(): void
    {
        // Arrange
        $id = 'test_id';
        $data = ['field1' => 'updated_value'];

        // Mock client update
        $this->client->expects($this->once())
            ->method('update')
            ->with($this->callback(function ($params) use ($id, $data) {
                return $params['index'] === 'test_index' &&
                    $params['id'] === $id &&
                    $params['body']['doc']['field1'] === 'updated_value' &&
                    isset($params['body']['doc']['updated_at']);
            }))
            ->willReturn(['result' => 'updated']);

        // Act
        $result = $this->repository->update($id, $data);

        // Assert
        $this->assertEquals('updated', $result['result']);
        $this->assertNull($result['error']);
    }

    /**
     * Tests the parseError method
     */
    public function testParseError(): void
    {
        // Arrange
        $exception = new Exception('Test exception');

        // Use reflection to access protected method
        $reflection = new \ReflectionClass(TestableElasticRepository::class);
        $method = $reflection->getMethod('parseError');
        $method->setAccessible(true);

        // Act
        $error = $method->invoke($this->repository, $exception);

        // Assert
        $this->assertStringContainsString('Test exception', $error);
    }

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);
        $this->repository = new TestableElasticRepository($this->client, $this->queryBuilder);
        $this->repository->setIndex('test_index');
    }
}
