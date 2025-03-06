<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Query;

use Jot\HfElastic\Query\QueryContext;
use PHPUnit\Framework\TestCase;

class QueryContextTest extends TestCase
{
    private QueryContext $queryContext;

    protected function setUp(): void
    {
        $this->queryContext = new QueryContext();
    }

    public function testSetAndGetIndex(): void
    {
        // Arrange
        $indexName = 'test_index';

        // Act
        $this->queryContext->setIndex($indexName);
        $result = $this->queryContext->getIndex();

        // Assert
        $this->assertEquals($indexName, $result, 'Index should be set and retrieved correctly');
    }

    public function testSetAndGetAdditionalIndices(): void
    {
        // Arrange
        $indices = ['index1', 'index2', 'index3'];

        // Act
        $this->queryContext->setAdditionalIndices($indices);
        $result = $this->queryContext->getAdditionalIndices();

        // Assert
        $this->assertEquals($indices, $result, 'Additional indices should be set and retrieved correctly');
    }

    public function testAddConditionWithMustContext(): void
    {
        // Arrange
        $condition = ['term' => ['name' => 'test']];
        $context = 'must';

        // Act
        $this->queryContext->addCondition($condition, $context);
        $query = $this->queryContext->getQuery();

        // Assert
        $this->assertArrayHasKey('bool', $query, 'Query should have a bool clause');
        $this->assertArrayHasKey($context, $query['bool'], "Query should have a '{$context}' clause");
        $this->assertCount(1, $query['bool'][$context], "The '{$context}' clause should have one condition");
        
        // For a term query
        $this->assertEquals($condition, $query['bool'][$context][0], 'Condition should be added as provided');
    }

    public function testAddConditionWithShouldContext(): void
    {
        // Arrange
        $condition = ['term' => ['category' => 'electronics']];
        $context = 'should';

        // Act
        $this->queryContext->addCondition($condition, $context);
        $query = $this->queryContext->getQuery();

        // Assert
        $this->assertArrayHasKey('bool', $query, 'Query should have a bool clause');
        $this->assertArrayHasKey($context, $query['bool'], "Query should have a '{$context}' clause");
        $this->assertCount(1, $query['bool'][$context], "The '{$context}' clause should have one condition");
        
        // For a term query
        $this->assertEquals($condition, $query['bool'][$context][0], 'Condition should be added as provided');
    }

    public function testAddConditionWithMustNotContext(): void
    {
        // Arrange
        $condition = ['term' => ['status' => 'inactive']];
        $context = 'must_not';

        // Act
        $this->queryContext->addCondition($condition, $context);
        $query = $this->queryContext->getQuery();

        // Assert
        $this->assertArrayHasKey('bool', $query, 'Query should have a bool clause');
        $this->assertArrayHasKey('must_not', $query['bool'], 'Query should have a must_not clause');
        $this->assertCount(1, $query['bool']['must_not'], 'The must_not clause should have one condition');
        
        // For a term query
        $this->assertEquals($condition, $query['bool']['must_not'][0], 'Condition should be added as provided');
    }

    public function testAddConditionWithRangeQuery(): void
    {
        // Arrange
        $condition = ['range' => ['price' => ['gt' => 100]]];
        $context = 'must';

        // Act
        $this->queryContext->addCondition($condition, $context);
        $query = $this->queryContext->getQuery();

        // Assert
        $this->assertArrayHasKey('bool', $query, 'Query should have a bool clause');
        $this->assertArrayHasKey($context, $query['bool'], "Query should have a '{$context}' clause");
        $this->assertCount(1, $query['bool'][$context], "The '{$context}' clause should have one condition");
        
        // For a range query
        $this->assertEquals($condition, $query['bool'][$context][0], 'Condition should be added as provided');
    }

    public function testSetBodyParam(): void
    {
        // Arrange
        $key = 'size';
        $value = 10;

        // Act
        $this->queryContext->setBodyParam($key, $value);
        $body = $this->queryContext->getBody();

        // Assert
        $this->assertArrayHasKey($key, $body, 'Body should have the specified parameter');
        $this->assertEquals($value, $body[$key], 'Parameter should match the specified value');
    }

    public function testSetMultipleBodyParams(): void
    {
        // Arrange
        $params = [
            'from' => 20,
            'size' => 10
        ];

        // Act
        foreach ($params as $key => $value) {
            $this->queryContext->setBodyParam($key, $value);
        }
        $body = $this->queryContext->getBody();

        // Assert
        foreach ($params as $key => $value) {
            $this->assertArrayHasKey($key, $body, 'Body should have the specified parameter');
            $this->assertEquals($value, $body[$key], 'Parameter should match the specified value');
        }
    }

    public function testAddAggregation(): void
    {
        // Arrange
        $name = 'price_stats';
        $aggregation = ['stats' => ['field' => 'price']];

        // Act
        $this->queryContext->addAggregation($name, $aggregation);
        $aggs = $this->queryContext->getAggregations();

        // Assert
        $this->assertArrayHasKey($name, $aggs, 'Aggregations should have the specified name');
        $this->assertEquals($aggregation, $aggs[$name], 'Aggregation should match the specified definition');
    }

    public function testToArrayReturnsCompleteQueryStructure(): void
    {
        // Arrange
        $indexName = 'test_index';
        $field = 'status';
        $value = 'active';
        $limit = 10;
        $offset = 20;
        
        $this->queryContext->setIndex($indexName);
        $this->queryContext->addCondition(['term' => [$field => $value]], 'must');
        $this->queryContext->setBodyParam('size', $limit);
        $this->queryContext->setBodyParam('from', $offset);
        $this->queryContext->setBodyParam('sort', [['created_at' => 'desc']]);

        // Act
        $result = $this->queryContext->toArray();

        // Assert
        $this->assertArrayHasKey('index', $result, 'Result should have an index parameter');
        $this->assertEquals($indexName, $result['index'], 'Index parameter should match the specified index');
        
        $this->assertArrayHasKey('body', $result, 'Result should have a body parameter');
        $this->assertArrayHasKey('size', $result['body'], 'Body should have a size parameter');
        $this->assertEquals($limit, $result['body']['size'], 'Size parameter should match the specified limit');
        
        $this->assertArrayHasKey('from', $result['body'], 'Body should have a from parameter');
        $this->assertEquals($offset, $result['body']['from'], 'From parameter should match the specified offset');
        
        $this->assertArrayHasKey('sort', $result['body'], 'Body should have a sort parameter');
        
        $this->assertArrayHasKey('query', $result['body'], 'Body should have a query parameter');
        $this->assertArrayHasKey('bool', $result['body']['query'], 'Query should have a bool clause');
        $this->assertArrayHasKey('must', $result['body']['query']['bool'], 'Bool clause should have a must clause');
        
        // Check that the filter for deleted=false is added
        $this->assertArrayHasKey('filter', $result['body']['query']['bool'], 'Bool clause should have a filter clause');
        $this->assertCount(1, $result['body']['query']['bool']['filter'], 'Filter clause should have one condition');
        $this->assertArrayHasKey('term', $result['body']['query']['bool']['filter'][0], 'Filter condition should be a term query');
        $this->assertArrayHasKey('deleted', $result['body']['query']['bool']['filter'][0]['term'], 'Filter term should target the deleted field');
        $this->assertFalse($result['body']['query']['bool']['filter'][0]['term']['deleted'], 'Filter term should have value false for deleted field');
    }

    public function testResetClearsAllState(): void
    {
        // Arrange
        $this->queryContext->setIndex('test_index');
        $this->queryContext->addCondition(['term' => ['status' => 'active']], 'must');
        $this->queryContext->setBodyParam('size', 10);
        $this->queryContext->setBodyParam('from', 20);
        $this->queryContext->setBodyParam('sort', [['created_at' => 'desc']]);

        // Act
        $this->queryContext->reset();

        // Assert
        $this->assertNull($this->queryContext->getIndex(), 'Index should be null after reset');
        $this->assertEmpty($this->queryContext->getQuery(), 'Query should be empty after reset');
        $this->assertEmpty($this->queryContext->getBody(), 'Body should be empty after reset');
    }
}
