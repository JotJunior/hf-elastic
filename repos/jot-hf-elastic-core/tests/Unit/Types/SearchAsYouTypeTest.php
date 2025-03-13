<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Tests\Unit\Types;

use Jot\HfElasticCore\Types\SearchAsYouType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElasticCore\Types\SearchAsYouType
 */
class SearchAsYouTypeTest extends TestCase
{
    private SearchAsYouType $type;
    private string $fieldName = 'product_name';

    protected function setUp(): void
    {
        $this->type = new SearchAsYouType($this->fieldName);
    }

    public function testConstructor(): void
    {
        $this->assertEquals($this->fieldName, $this->type->getName());
        $this->assertEquals('search_as_you_type', $this->type->getType());
        $this->assertTrue($this->type->isSearchable());
        $this->assertFalse($this->type->isFilterable());
        $this->assertFalse($this->type->isSortable());
    }

    public function testSetAnalyzer(): void
    {
        $analyzer = 'standard';
        $result = $this->type->setAnalyzer($analyzer);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals($analyzer, $this->type->getProperty('analyzer'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals($analyzer, $mapping['analyzer']);
    }

    public function testSetSearchAnalyzer(): void
    {
        $searchAnalyzer = 'standard';
        $result = $this->type->setSearchAnalyzer($searchAnalyzer);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals($searchAnalyzer, $this->type->getProperty('search_analyzer'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals($searchAnalyzer, $mapping['search_analyzer']);
    }

    public function testSetIndexOptions(): void
    {
        $indexOptions = 'docs';
        $result = $this->type->setIndexOptions($indexOptions);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals($indexOptions, $this->type->getProperty('index_options'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals($indexOptions, $mapping['index_options']);
    }

    public function testSetMaxShingleSize(): void
    {
        $maxShingleSize = 3;
        $result = $this->type->setMaxShingleSize($maxShingleSize);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals($maxShingleSize, $this->type->getProperty('max_shingle_size'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals($maxShingleSize, $mapping['max_shingle_size']);
    }

    public function testSetStore(): void
    {
        $result = $this->type->setStore(true);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals(true, $this->type->getProperty('store'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals(true, $mapping['store']);
    }

    public function testSetSimilarity(): void
    {
        $similarity = 'BM25';
        $result = $this->type->setSimilarity($similarity);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals($similarity, $this->type->getProperty('similarity'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals($similarity, $mapping['similarity']);
    }

    public function testSetTermVector(): void
    {
        $termVector = 'with_positions_offsets';
        $result = $this->type->setTermVector($termVector);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals($termVector, $this->type->getProperty('term_vector'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals($termVector, $mapping['term_vector']);
    }

    public function testSetBoost(): void
    {
        $boost = 1.5;
        $result = $this->type->setBoost($boost);
        
        $this->assertSame($this->type, $result);
        $this->assertEquals($boost, $this->type->getProperty('boost'));
        
        $mapping = $this->type->toMapping();
        $this->assertEquals($boost, $mapping['boost']);
    }

    public function testToMapping(): void
    {
        $this->type->setAnalyzer('standard')
            ->setSearchAnalyzer('standard')
            ->setIndexOptions('docs')
            ->setMaxShingleSize(3)
            ->setStore(true)
            ->setSimilarity('BM25')
            ->setTermVector('with_positions_offsets')
            ->setBoost(2.0);

        $expected = [
            'type' => 'search_as_you_type',
            'analyzer' => 'standard',
            'search_analyzer' => 'standard',
            'index_options' => 'docs',
            'max_shingle_size' => 3,
            'store' => true,
            'similarity' => 'BM25',
            'term_vector' => 'with_positions_offsets',
            'boost' => 2.0,
        ];

        $this->assertEquals($expected, $this->type->toMapping());
    }

    public function testChainability(): void
    {
        $result = $this->type
            ->setAnalyzer('standard')
            ->setSearchAnalyzer('standard')
            ->setIndexOptions('docs')
            ->setMaxShingleSize(3)
            ->setStore(true)
            ->setSimilarity('BM25')
            ->setTermVector('with_positions_offsets')
            ->setBoost(1.0);
        
        $this->assertSame($this->type, $result);
    }
}
