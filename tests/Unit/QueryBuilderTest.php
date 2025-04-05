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
use Jot\HfElastic\ClientBuilder;
use Jot\HfElastic\Contracts\ClientFactoryInterface;
use Jot\HfElastic\Query\ElasticQueryBuilder;
use Jot\HfElastic\Query\OperatorRegistry;
use Jot\HfElastic\Query\Operators\EqualsOperator;
use Jot\HfElastic\Query\QueryContext;
use Jot\HfElastic\QueryBuilder;
use Jot\HfElastic\Services\IndexNameFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @internal
 * @coversNothing
 */
class QueryBuilderTest extends TestCase
{
    private QueryBuilder $queryBuilder;

    private Client|MockObject $client;

    private IndexNameFormatter|MockObject $indexFormatter;

    private MockObject|OperatorRegistry $operatorRegistry;

    private MockObject|QueryContext $queryContext;

    private ClientFactoryInterface|MockObject $clientFactory;

    protected function setUp(): void
    {
        // Create mocks for dependencies
        $this->client = $this->createMock(Client::class);
        $this->indexFormatter = $this->createMock(IndexNameFormatter::class);
        $this->operatorRegistry = $this->createMock(OperatorRegistry::class);
        $this->queryContext = $this->createMock(QueryContext::class);
        $this->clientFactory = $this->createMock(ClientFactoryInterface::class);

        // Create a mock for ClientBuilder
        $clientBuilder = $this->createMock(ClientBuilder::class);
        $clientBuilder->method('build')
            ->willReturn($this->client);

        // Create the query builder instance with mocked dependencies
        $this->queryBuilder = new QueryBuilder(
            $clientBuilder,
            $this->indexFormatter,
            $this->operatorRegistry,
            $this->queryContext
        );
    }

    public function testQueryBuilderExtendsElasticQueryBuilder(): void
    {
        // Assert
        $this->assertInstanceOf(ElasticQueryBuilder::class, $this->queryBuilder, 'QueryBuilder should extend ElasticQueryBuilder');
    }

    public function testIntoMethodInheritsFromElasticQueryBuilder(): void
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

    public function testFromMethodInheritsFromElasticQueryBuilder(): void
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

    public function testWhereMethodInheritsFromElasticQueryBuilder(): void
    {
        // Arrange
        $field = 'name';
        $operator = '=';
        $value = 'test';
        $context = 'must';

        // Create a mock for the EqualsOperator
        $equalsOperator = $this->createMock(EqualsOperator::class);
        $equalsOperator->method('supports')
            ->with($operator)
            ->willReturn(true);
        $equalsOperator->method('apply')
            ->willReturn(['term' => [$field => $value]]);

        // Setup expectations for operator registry
        $this->operatorRegistry->expects($this->once())
            ->method('findStrategy')
            ->with($operator)
            ->willReturn($equalsOperator);

        // The query context should be updated with the condition
        $this->queryContext->expects($this->once())
            ->method('addCondition')
            ->with($this->isType('array'), $this->equalTo($context));

        // Act
        $result = $this->queryBuilder->where($field, $operator, $value, $context);

        // Assert
        $this->assertSame($this->queryBuilder, $result, 'Method should return the builder instance for chaining');
    }

    public function testExecuteMethodInheritsFromElasticQueryBuilder(): void
    {
        // Arrange
        $searchParams = ['index' => 'test_index', 'body' => ['query' => ['match_all' => new stdClass()]]];
        $searchResult = [
            'hits' => [
                'hits' => [
                    ['_source' => ['id' => 1, 'name' => 'Test 1']],
                    ['_source' => ['id' => 2, 'name' => 'Test 2']],
                ],
            ],
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
}
