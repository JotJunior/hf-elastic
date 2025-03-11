<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Migration\ElasticType;

use Jot\HfElastic\Migration\ElasticType\SearchAsYouType;
use Jot\HfElastic\Migration\ElasticType\Type;
use PHPUnit\Framework\TestCase;

class SearchAsYouTypeTest extends TestCase
{
    private SearchAsYouType $type;
    
    protected function setUp(): void
    {
        $this->type = new SearchAsYouType('test_field');
    }
    
    public function testConstructor(): void
    {
        $this->assertEquals('test_field', $this->type->getName());
        $this->assertEquals(Type::searchAsYouType, $this->type->getType());
    }
    
    public function testAnalyzer(): void
    {
        $result = $this->type->analyzer('standard');
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertEquals('standard', $options['analyzer']);
    }
    
    public function testSearchAnalyzer(): void
    {
        $result = $this->type->searchAnalyzer('standard');
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertEquals('standard', $options['search_analyzer']);
    }
    
    public function testSearchQuoteAnalyzer(): void
    {
        $result = $this->type->searchQuoteAnalyzer('standard');
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertEquals('standard', $options['search_quote_analyzer']);
    }
    
    public function testMaxShingleSize(): void
    {
        $result = $this->type->maxShingleSize(3);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertEquals(3, $options['max_shingle_size']);
    }
    
    public function testIndex(): void
    {
        $result = $this->type->index(false);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertFalse($options['index']);
    }
    
    public function testNorms(): void
    {
        $result = $this->type->norms(false);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertFalse($options['norms']);
    }
    
    public function testStore(): void
    {
        $result = $this->type->store(true);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertTrue($options['store']);
    }
    
    public function testSimilarity(): void
    {
        $result = $this->type->similarity('BM25');
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertEquals('BM25', $options['similarity']);
    }
    
    public function testTermVector(): void
    {
        $result = $this->type->termVector('with_positions_offsets');
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertEquals('with_positions_offsets', $options['term_vector']);
    }
    
    public function testCopyToWithString(): void
    {
        $result = $this->type->copyTo('another_field');
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertEquals('another_field', $options['copy_to']);
    }
    
    public function testCopyToWithArray(): void
    {
        $copyToFields = ['field1', 'field2'];
        $result = $this->type->copyTo($copyToFields);
        $this->assertSame($this->type, $result);
        $options = $this->type->getOptions();
        $this->assertEquals($copyToFields, $options['copy_to']);
    }
}
