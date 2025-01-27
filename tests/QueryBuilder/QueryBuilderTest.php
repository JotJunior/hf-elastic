<?php

namespace Jot\HfElasticTest\QueryBuilder;

use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{

    use QueryBuilderTestTrait;

    public function testQueryExecution()
    {
        $expectedResult = [
            'test_field' => 'test_value',
        ];

        $queryBuilder = $this->createQueryBuilderWithMocks([['_source' => $expectedResult]]);
        $queryBuilder
            ->from('test_index')
            ->select('*')
            ->where('test_field', '=', 'test_value');
        $result = $queryBuilder->execute();

        $this->assertQueryResult($result, $expectedResult);
    }

    private function assertQueryResult(array $result, array $expectedData): void
    {
        $this->assertEquals('success', $result['result']);
        $this->assertNull($result['error']);
        $this->assertCount(1, $result['data']);
        $this->assertArrayHasKey('test_field', $result['data'][0]);
        $this->assertEquals($expectedData['test_field'], $result['data'][0]['test_field']);
    }

}