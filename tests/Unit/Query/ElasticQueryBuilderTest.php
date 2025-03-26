<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Query;

use Elasticsearch\Client;
use Jot\HfElastic\ClientBuilder;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Query\ElasticQueryBuilder;
use Jot\HfElastic\Query\OperatorRegistry;
use Jot\HfElastic\Query\QueryContext;
use Jot\HfElastic\Services\IndexNameFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Query\ElasticQueryBuilder
 * @group unit
 */
class ElasticQueryBuilderTest extends TestCase
{
    private ElasticQueryBuilder $queryBuilder;
    private Client|MockObject $client;
    private IndexNameFormatter|MockObject $indexFormatter;
    private OperatorRegistry|MockObject $operatorRegistry;
    private QueryContext|MockObject $queryContext;

    protected function setUp(): void
    {
        // Create mocks for dependencies
        $this->client = $this->createMock(Client::class);
        $this->indexFormatter = $this->createMock(IndexNameFormatter::class);
        
        // Create a real OperatorRegistry with a mock for the equals operator
        $this->operatorRegistry = new OperatorRegistry();
        $equalsOperator = $this->createMock(\Jot\HfElastic\Contracts\OperatorStrategyInterface::class);
        $equalsOperator->method('supports')
            ->with('=')
            ->willReturn(true);
        $equalsOperator->method('apply')
            ->willReturn(['term' => ['field' => 'value']]);
        $this->operatorRegistry->register($equalsOperator);
        
        $this->queryContext = $this->createMock(QueryContext::class);

        // Create a mock for ClientBuilder
        $clientBuilder = $this->createMock(ClientBuilder::class);
        $clientBuilder->method('build')
            ->willReturn($this->client);
            
        // Create the query builder instance with mocked dependencies
        $this->queryBuilder = new ElasticQueryBuilder(
            $clientBuilder,
            $this->indexFormatter,
            $this->operatorRegistry,
            $this->queryContext
        );
    }

    public function testIntoMethodSetsIndexAndReturnsInstance(): void
    {
        // Arrange
        $indexName = 'test_index';
        $formattedIndex = 'formatted_test_index';
        
        // Setup expectations
        $this->indexFormatter->expects($this->once())
            ->method('format')
            ->with($indexName)
            ->willReturn($formattedIndex);
            
        $this->queryContext->expects($this->once())
            ->method('setIndex')
            ->with($formattedIndex);

        // Act
        $result = $this->queryBuilder->into($indexName);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testFromMethodSetsIndexAndReturnsInstance(): void
    {
        // Arrange
        $indexName = 'test_index';
        $formattedIndex = 'formatted_test_index';
        
        // Setup expectations
        $this->indexFormatter->expects($this->once())
            ->method('format')
            ->with($indexName)
            ->willReturn($formattedIndex);
            
        $this->queryContext->expects($this->once())
            ->method('setIndex')
            ->with($formattedIndex);

        // Act
        $result = $this->queryBuilder->from($indexName);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testWhereMethodWithEqualsOperator(): void
    {
        // Arrange
        $field = 'name';
        $operator = '=';
        $value = 'test';
        $context = 'must';
        
        // The query context should be updated with the condition
        $this->queryContext->expects($this->once())
            ->method('addCondition')
            ->with($this->isType('array'), $this->equalTo('must'));

        // Act
        $result = $this->queryBuilder->where($field, $operator, $value, $context);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testOrWhereMethodAddsConditionWithShouldContext(): void
    {
        // Arrange
        $field = 'category';
        $operator = '=';
        $value = 'electronics';
        
        // No need to set up expectations for the operator registry
        // as we've already set it up in setUp() method
            
        // The query context should be updated with the condition using 'should' context
        $this->queryContext->expects($this->once())
            ->method('addCondition')
            ->with($this->isType('array'), $this->equalTo('should'));

        // Act
        $result = $this->queryBuilder->orWhere($field, $operator, $value);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testLimitMethodSetsLimitInQueryContext(): void
    {
        // Arrange
        $limit = 10;
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('setBodyParam')
            ->with('size', $limit);

        // Act
        $result = $this->queryBuilder->limit($limit);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testOffsetMethodSetsOffsetInQueryContext(): void
    {
        // Arrange
        $offset = 20;
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('setBodyParam')
            ->with('from', $offset);

        // Act
        $result = $this->queryBuilder->offset($offset);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testOrderByMethodAddsSortCriteriaToQueryContext(): void
    {
        // Arrange
        $field = 'created_at';
        $order = 'desc';
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('setBodyParam')
            ->with('sort', [[$field => $order]]);

        // Act
        $result = $this->queryBuilder->orderBy($field, $order);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testExecuteMethodReturnsSearchResults(): void
    {
        // Arrange
        $searchParams = ['index' => 'test_index', 'body' => ['query' => ['match_all' => new \stdClass()]]];
        $searchResult = [
            'hits' => [
                'hits' => [
                    ['_source' => ['id' => 1, 'name' => 'Test 1']],
                    ['_source' => ['id' => 2, 'name' => 'Test 2']],
                ]
            ]
        ];
        $expectedResult = [
            'data' => [
                ['id' => 1, 'name' => 'Test 1'],
                ['id' => 2, 'name' => 'Test 2'],
            ],
            'result' => 'success',
            'error' => null,
        ];
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('toArray')
            ->willReturn($searchParams);
            
        $this->client->expects($this->once())
            ->method('search')
            ->with($searchParams)
            ->willReturn($searchResult);
            
        $this->queryContext->expects($this->once())
            ->method('reset');

        // Act
        $result = $this->queryBuilder->execute();

        // Assert
        $this->assertEquals($expectedResult, $result, 'Execute should return formatted search results');
    }

    public function testExecuteMethodHandlesExceptions(): void
    {
        // Arrange
        $searchParams = ['index' => 'test_index', 'body' => ['query' => ['match_all' => new \stdClass()]]];
        $exceptionMessage = '{"error":{"reason":"Invalid query"}}'; // JSON formatted error
        $expectedException = new \Exception($exceptionMessage);
        $expectedResult = [
            'data' => null,
            'result' => 'error',
            'error' => 'Invalid query', // Parsed from the exception
        ];
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('toArray')
            ->willReturn($searchParams);
            
        $this->client->expects($this->once())
            ->method('search')
            ->with($searchParams)
            ->willThrowException($expectedException);

        // Act
        $result = $this->queryBuilder->execute();

        // Assert
        $this->assertEquals($expectedResult, $result, 'Execute should handle exceptions and return error information');
    }

    public function testCountMethodReturnsDocumentCount(): void
    {
        // Arrange
        $searchParams = ['index' => 'test_index', 'body' => ['query' => ['match_all' => new \stdClass()]]];
        $countResult = ['count' => 42];
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('toArray')
            ->willReturn($searchParams);
            
        // We now access the property directly instead of calling a method
        // No need to mock this anymore
            
        $this->client->expects($this->once())
            ->method('count')
            ->willReturn($countResult);
            
        $this->queryContext->expects($this->once())
            ->method('reset');

        // Act
        $result = $this->queryBuilder->count();

        // Assert
        $this->assertEquals(42, $result, 'Count should return the number of matching documents');
    }

    public function testJoinMethodAddsAdditionalIndices(): void
    {
        // Arrange
        $indices = ['index1', 'index2'];
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('setAdditionalIndices')
            ->with($indices);

        // Act
        $result = $this->queryBuilder->join($indices);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testJoinMethodWithSingleIndex(): void
    {
        // Arrange
        $index = 'index1';
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('setAdditionalIndices')
            ->with([$index]);

        // Act
        $result = $this->queryBuilder->join($index);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testAndWhereMethod(): void
    {
        // Arrange
        $field = 'status';
        $operator = '=';
        $value = 'active';
        $context = 'must';
        
        // The query context should be updated with the condition
        $this->queryContext->expects($this->once())
            ->method('addCondition')
            ->with($this->isType('array'), $this->equalTo('must'));

        // Act
        $result = $this->queryBuilder->andWhere($field, $operator, $value, $context);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testGeoDistanceMethod(): void
    {
        // Arrange
        $field = 'location';
        $location = '40.73, -74.1';
        $distance = '10km';
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('addCondition')
            ->with(
                $this->equalTo(['geo_distance' => ['distance' => $distance, 'location' => $location]]),
                $this->equalTo('must')
            );

        // Act
        $result = $this->queryBuilder->geoDistance($field, $location, $distance);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testSelectMethodWithArray(): void
    {
        // Arrange
        $fields = ['id', 'name', 'email'];
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('setBodyParam')
            ->with('_source', $fields);

        // Act
        $result = $this->queryBuilder->select($fields);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testSelectMethodWithString(): void
    {
        // Arrange
        $fields = '*';
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('setBodyParam')
            ->with('_source', []);

        // Act
        $result = $this->queryBuilder->select($fields);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testToArrayMethod(): void
    {
        // Arrange
        $expectedArray = ['index' => 'test_index', 'body' => ['query' => ['match_all' => new \stdClass()]]];
        
        // Setup expectations
        $this->queryContext->expects($this->once())
            ->method('toArray')
            ->willReturn($expectedArray);

        // Act
        $result = $this->queryBuilder->toArray();

        // Assert
        $this->assertEquals($expectedArray, $result, 'toArray should return the query context as an array');
    }
}
