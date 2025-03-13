<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Tests\Unit\Query;

use InvalidArgumentException;
use Jot\HfElasticCore\Client\ElasticClient;
use Jot\HfElasticQuery\Context\QueryContext;
use Jot\HfElasticQuery\Contracts\OperatorInterface;
use Jot\HfElasticQuery\Contracts\OperatorRegistryInterface;
use Jot\HfElasticQuery\Contracts\QueryContextInterface;
use Jot\HfElasticQuery\Query\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElasticQuery\Query\QueryBuilder
 */
class QueryBuilderTest extends TestCase
{
    private MockObject|ElasticClient $client;
    private MockObject|OperatorRegistryInterface $operatorRegistry;
    private MockObject|QueryContextInterface $context;
    private QueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ElasticClient::class);
        $this->operatorRegistry = $this->createMock(OperatorRegistryInterface::class);
        $this->context = $this->createMock(QueryContextInterface::class);
        
        $this->queryBuilder = new QueryBuilder(
            $this->client,
            $this->operatorRegistry,
            $this->context
        );
    }

    public function testIndexSetsIndexInContext(): void
    {
        $indexName = 'products';
        
        $this->context->expects($this->once())
            ->method('setIndex')
            ->with($indexName);
        
        $result = $this->queryBuilder->index($indexName);
        
        $this->assertSame($this->queryBuilder, $result);
    }

    public function testWhereWithThreeArguments(): void
    {
        $field = 'name';
        $operator = '=';
        $value = 'test';
        
        $operatorMock = $this->createMock(OperatorInterface::class);
        $operatorMock->expects($this->once())
            ->method('supports')
            ->with($value)
            ->willReturn(true);
        
        $operatorMock->expects($this->once())
            ->method('apply')
            ->with($field, $value)
            ->willReturn(['term' => [$field => $value]]);
        
        $this->operatorRegistry->expects($this->once())
            ->method('has')
            ->with($operator)
            ->willReturn(true);
        
        $this->operatorRegistry->expects($this->once())
            ->method('get')
            ->with($operator)
            ->willReturn($operatorMock);
        
        $this->context->expects($this->once())
            ->method('addFilter')
            ->with(['term' => [$field => $value]]);
        
        $result = $this->queryBuilder->where($field, $operator, $value);
        
        $this->assertSame($this->queryBuilder, $result);
    }

    public function testWhereWithTwoArguments(): void
    {
        $field = 'name';
        $value = 'test';
        
        $operatorMock = $this->createMock(OperatorInterface::class);
        $operatorMock->expects($this->once())
            ->method('supports')
            ->with($value)
            ->willReturn(true);
        
        $operatorMock->expects($this->once())
            ->method('apply')
            ->with($field, $value)
            ->willReturn(['term' => [$field => $value]]);
        
        $this->operatorRegistry->expects($this->once())
            ->method('has')
            ->with('=')
            ->willReturn(true);
        
        $this->operatorRegistry->expects($this->once())
            ->method('get')
            ->with('=')
            ->willReturn($operatorMock);
        
        $this->context->expects($this->once())
            ->method('addFilter')
            ->with(['term' => [$field => $value]]);
        
        $result = $this->queryBuilder->where($field, $value);
        
        $this->assertSame($this->queryBuilder, $result);
    }

    public function testWhereThrowsExceptionForUnregisteredOperator(): void
    {
        $field = 'name';
        $operator = 'invalid_operator';
        $value = 'test';
        
        $this->operatorRegistry->expects($this->once())
            ->method('has')
            ->with($operator)
            ->willReturn(false);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Operator "%s" is not registered.',
            $operator
        ));
        
        $this->queryBuilder->where($field, $operator, $value);
    }

    public function testWhereThrowsExceptionForUnsupportedValue(): void
    {
        $field = 'name';
        $operator = '=';
        $value = 'test';
        
        $operatorMock = $this->createMock(OperatorInterface::class);
        $operatorMock->expects($this->once())
            ->method('supports')
            ->with($value)
            ->willReturn(false);
        
        $this->operatorRegistry->expects($this->once())
            ->method('has')
            ->with($operator)
            ->willReturn(true);
        
        $this->operatorRegistry->expects($this->once())
            ->method('get')
            ->with($operator)
            ->willReturn($operatorMock);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Operator "%s" does not support the provided value.',
            $operator
        ));
        
        $this->queryBuilder->where($field, $operator, $value);
    }

    public function testWhereInCallsWhereWithInOperator(): void
    {
        $field = 'category';
        $values = ['electronics', 'computers'];
        
        $operatorMock = $this->createMock(OperatorInterface::class);
        $operatorMock->expects($this->once())
            ->method('supports')
            ->with($values)
            ->willReturn(true);
        
        $operatorMock->expects($this->once())
            ->method('apply')
            ->with($field, $values)
            ->willReturn(['terms' => [$field => $values]]);
        
        $this->operatorRegistry->expects($this->once())
            ->method('has')
            ->with('in')
            ->willReturn(true);
        
        $this->operatorRegistry->expects($this->once())
            ->method('get')
            ->with('in')
            ->willReturn($operatorMock);
        
        $this->context->expects($this->once())
            ->method('addFilter')
            ->with(['terms' => [$field => $values]]);
        
        $result = $this->queryBuilder->whereIn($field, $values);
        
        $this->assertSame($this->queryBuilder, $result);
    }

    public function testWhereNotInCallsWhereWithNotInOperator(): void
    {
        $field = 'category';
        $values = ['electronics', 'computers'];
        
        $operatorMock = $this->createMock(OperatorInterface::class);
        $operatorMock->expects($this->once())
            ->method('supports')
            ->with($values)
            ->willReturn(true);
        
        $operatorMock->expects($this->once())
            ->method('apply')
            ->with($field, $values)
            ->willReturn(['bool' => ['must_not' => ['terms' => [$field => $values]]]]);
        
        $this->operatorRegistry->expects($this->once())
            ->method('has')
            ->with('not in')
            ->willReturn(true);
        
        $this->operatorRegistry->expects($this->once())
            ->method('get')
            ->with('not in')
            ->willReturn($operatorMock);
        
        $this->context->expects($this->once())
            ->method('addFilter')
            ->with(['bool' => ['must_not' => ['terms' => [$field => $values]]]]);
        
        $result = $this->queryBuilder->whereNotIn($field, $values);
        
        $this->assertSame($this->queryBuilder, $result);
    }

    public function testWhereBetweenCallsWhereWithBetweenOperator(): void
    {
        $field = 'price';
        $from = 10;
        $to = 100;
        
        $operatorMock = $this->createMock(OperatorInterface::class);
        $operatorMock->expects($this->once())
            ->method('supports')
            ->with([$from, $to])
            ->willReturn(true);
        
        $operatorMock->expects($this->once())
            ->method('apply')
            ->with($field, [$from, $to])
            ->willReturn(['range' => [$field => ['gte' => $from, 'lte' => $to]]]);
        
        $this->operatorRegistry->expects($this->once())
            ->method('has')
            ->with('between')
            ->willReturn(true);
        
        $this->operatorRegistry->expects($this->once())
            ->method('get')
            ->with('between')
            ->willReturn($operatorMock);
        
        $this->context->expects($this->once())
            ->method('addFilter')
            ->with(['range' => [$field => ['gte' => $from, 'lte' => $to]]]);
        
        $result = $this->queryBuilder->whereBetween($field, $from, $to);
        
        $this->assertSame($this->queryBuilder, $result);
    }

    public function testOrderBySetsCorrectSortInContext(): void
    {
        $field = 'created_at';
        $direction = 'desc';
        
        $this->context->expects($this->once())
            ->method('addSort')
            ->with($field, $direction);
        
        $result = $this->queryBuilder->orderBy($field, $direction);
        
        $this->assertSame($this->queryBuilder, $result);
    }

    public function testOrderByThrowsExceptionForInvalidDirection(): void
    {
        $field = 'created_at';
        $direction = 'invalid';
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Direction must be "asc" or "desc".');
        
        $this->queryBuilder->orderBy($field, $direction);
    }

    public function testLimitSetsSizeInContext(): void
    {
        $limit = 10;
        
        $this->context->expects($this->once())
            ->method('setSize')
            ->with($limit);
        
        $result = $this->queryBuilder->limit($limit);
        
        $this->assertSame($this->queryBuilder, $result);
    }

    public function testOffsetSetsFromInContext(): void
    {
        $offset = 20;
        
        $this->context->expects($this->once())
            ->method('setFrom')
            ->with($offset);
        
        $result = $this->queryBuilder->offset($offset);
        
        $this->assertSame($this->queryBuilder, $result);
    }

    public function testGetReturnsFormattedResults(): void
    {
        $params = ['index' => 'products', 'body' => ['query' => ['match_all' => new \stdClass()]]];
        $searchResult = [
            'hits' => [
                'total' => ['value' => 2],
                'hits' => [
                    [
                        '_id' => '1',
                        '_score' => 1.0,
                        '_source' => ['name' => 'Product 1', 'price' => 100],
                    ],
                    [
                        '_id' => '2',
                        '_score' => 0.8,
                        '_source' => ['name' => 'Product 2', 'price' => 200],
                    ],
                ],
            ],
        ];
        
        $this->context->expects($this->once())
            ->method('build')
            ->willReturn($params);
        
        $this->client->expects($this->once())
            ->method('search')
            ->with($params)
            ->willReturn($searchResult);
        
        $expected = [
            'total' => 2,
            'hits' => [
                ['name' => 'Product 1', 'price' => 100, '_id' => '1', '_score' => 1.0],
                ['name' => 'Product 2', 'price' => 200, '_id' => '2', '_score' => 0.8],
            ],
        ];
        
        $result = $this->queryBuilder->get();
        
        $this->assertEquals($expected, $result);
    }

    public function testFirstReturnsFirstResult(): void
    {
        $params = ['index' => 'products', 'body' => ['query' => ['match_all' => new \stdClass()], 'size' => 1]];
        $searchResult = [
            'hits' => [
                'total' => ['value' => 1],
                'hits' => [
                    [
                        '_id' => '1',
                        '_score' => 1.0,
                        '_source' => ['name' => 'Product 1', 'price' => 100],
                    ],
                ],
            ],
        ];
        
        $this->context->expects($this->once())
            ->method('setSize')
            ->with(1);
        
        $this->context->expects($this->once())
            ->method('build')
            ->willReturn($params);
        
        $this->client->expects($this->once())
            ->method('search')
            ->with($params)
            ->willReturn($searchResult);
        
        $expected = ['name' => 'Product 1', 'price' => 100, '_id' => '1', '_score' => 1.0];
        
        $result = $this->queryBuilder->first();
        
        $this->assertEquals($expected, $result);
    }

    public function testCountReturnsNumberOfResults(): void
    {
        $params = ['index' => 'products', 'body' => ['query' => ['match_all' => new \stdClass()]]];
        $countResult = ['count' => 42];
        
        $this->context->expects($this->once())
            ->method('build')
            ->willReturn($params);
        
        $this->client->expects($this->once())
            ->method('count')
            ->with(array_merge($params, ['body' => ['size' => 0, 'query' => ['match_all' => new \stdClass()]]]))
            ->willReturn($countResult);
        
        $result = $this->queryBuilder->count();
        
        $this->assertEquals(42, $result);
    }

    public function testPaginateReturnsFormattedPaginationResults(): void
    {
        $perPage = 10;
        $page = 2;
        $offset = 10; // (page - 1) * perPage
        
        $params = ['index' => 'products', 'body' => ['query' => ['match_all' => new \stdClass()], 'size' => $perPage, 'from' => $offset]];
        $searchResult = [
            'hits' => [
                'total' => ['value' => 25],
                'hits' => [
                    [
                        '_id' => '11',
                        '_score' => 1.0,
                        '_source' => ['name' => 'Product 11'],
                    ],
                    [
                        '_id' => '12',
                        '_score' => 0.9,
                        '_source' => ['name' => 'Product 12'],
                    ],
                ],
            ],
        ];
        
        $this->context->expects($this->once())
            ->method('setSize')
            ->with($perPage);
        
        $this->context->expects($this->once())
            ->method('setFrom')
            ->with($offset);
        
        $this->context->expects($this->once())
            ->method('build')
            ->willReturn($params);
        
        $this->client->expects($this->once())
            ->method('search')
            ->with($params)
            ->willReturn($searchResult);
        
        $expected = [
            'total' => 25,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => 3,
            'from' => 11,
            'to' => 20,
            'data' => [
                ['name' => 'Product 11', '_id' => '11', '_score' => 1.0],
                ['name' => 'Product 12', '_id' => '12', '_score' => 0.9],
            ],
        ];
        
        $result = $this->queryBuilder->paginate($perPage, $page);
        
        $this->assertEquals($expected, $result);
    }

    public function testToArrayReturnsContextBuild(): void
    {
        $params = ['index' => 'products', 'body' => ['query' => ['match_all' => new \stdClass()]]];
        
        $this->context->expects($this->once())
            ->method('build')
            ->willReturn($params);
        
        $result = $this->queryBuilder->toArray();
        
        $this->assertEquals($params, $result);
    }

    public function testConstructorCreatesDefaultContextIfNoneProvided(): void
    {
        $queryBuilder = new QueryBuilder(
            $this->client,
            $this->operatorRegistry
        );
        
        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
    }
}
