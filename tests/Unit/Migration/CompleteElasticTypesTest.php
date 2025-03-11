<?php

namespace Jot\HfElastic\Tests\Unit\Migration;

use Jot\HfElastic\Migration\ElasticType;
use Jot\HfElastic\Migration\Mapping;
use PHPUnit\Framework\TestCase;

class CompleteElasticTypesTest extends TestCase
{
    private Mapping $mapping;
    
    protected function setUp(): void
    {
        // Carrega o mapeamento completo do arquivo de exemplo
        $this->mapping = require __DIR__ . '/../../Examples/mapping/mapping-test.php';
    }
    
    public function testMappingName(): void
    {
        $this->assertEquals('complete-test-index', $this->mapping->getName());
    }
    
    public function testMappingSettings(): void
    {
        $body = $this->mapping->body();
        
        $this->assertArrayHasKey('settings', $body['body']);
        $this->assertEquals(3, $body['body']['settings']['number_of_shards']);
        $this->assertEquals(2, $body['body']['settings']['number_of_replicas']);
        $this->assertEquals('1s', $body['body']['settings']['refresh_interval']);
        
        // Verificar configurações de análise
        $this->assertArrayHasKey('analysis', $body['body']['settings']);
        $this->assertArrayHasKey('analyzer', $body['body']['settings']['analysis']);
        $this->assertArrayHasKey('normalizer', $body['body']['settings']['analysis']);
    }
    
    public function testTextType(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        $this->assertArrayHasKey('description', $properties);
        $this->assertEquals('text', $properties['description']['type']);
        $this->assertEquals('custom_analyzer', $properties['description']['analyzer']);
        $this->assertTrue($properties['description']['eager_global_ordinals']);
        $this->assertTrue($properties['description']['fielddata']);
        $this->assertEquals('positions', $properties['description']['index_options']);
        $this->assertFalse($properties['description']['norms']);
        $this->assertTrue($properties['description']['store']);
        $this->assertEquals('standard', $properties['description']['search_analyzer']);
        $this->assertEquals('BM25', $properties['description']['similarity']);
    }
    
    public function testKeywordType(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        $this->assertArrayHasKey('code', $properties);
        $this->assertEquals('keyword', $properties['code']['type']);
        $this->assertTrue($properties['code']['doc_values']);
        $this->assertTrue($properties['code']['eager_global_ordinals']);
        $this->assertEquals(256, $properties['code']['ignore_above']);
        $this->assertEquals('docs', $properties['code']['index_options']);
        $this->assertFalse($properties['code']['norms']);
        $this->assertEquals('N/A', $properties['code']['null_value']);
        $this->assertTrue($properties['code']['store']);
        $this->assertEquals('my_normalizer', $properties['code']['normalizer']);
        $this->assertTrue($properties['code']['split_queries_on_whitespace']);
    }
    
    public function testNumericTypes(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        // Integer
        $this->assertArrayHasKey('age', $properties);
        $this->assertEquals('integer', $properties['age']['type']);
        $this->assertTrue($properties['age']['coerce']);
        $this->assertTrue($properties['age']['ignore_malformed']);
        $this->assertEquals(0, $properties['age']['null_value']);
        
        // Long
        $this->assertArrayHasKey('user_id', $properties);
        $this->assertEquals('long', $properties['user_id']['type']);
        
        // Float
        $this->assertArrayHasKey('score', $properties);
        $this->assertEquals('float', $properties['score']['type']);
        
        // Double
        $this->assertArrayHasKey('precise_score', $properties);
        $this->assertEquals('double', $properties['precise_score']['type']);
        
        // HalfFloat
        $this->assertArrayHasKey('approximate_value', $properties);
        $this->assertEquals('half_float', $properties['approximate_value']['type']);
        
        // ScaledFloat
        $this->assertArrayHasKey('price', $properties);
        $this->assertEquals('scaled_float', $properties['price']['type']);
        $this->assertEquals(100, $properties['price']['scaling_factor']);
        
        // UnsignedLong
        $this->assertArrayHasKey('positive_number', $properties);
        $this->assertEquals('unsigned_long', $properties['positive_number']['type']);
    }
    
    public function testDateTypes(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        // Date
        $this->assertArrayHasKey('created_at', $properties);
        $this->assertEquals('date', $properties['created_at']['type']);
        $this->assertEquals('yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis', $properties['created_at']['format']);
        $this->assertTrue($properties['created_at']['ignore_malformed']);
        $this->assertEquals('1970-01-01', $properties['created_at']['null_value']);
        
        // DateNanos
        $this->assertArrayHasKey('updated_at_precise', $properties);
        $this->assertEquals('date_nanos', $properties['updated_at_precise']['type']);
        $this->assertEquals('yyyy-MM-dd HH:mm:ss.SSSSSS||strict_date_optional_time_nanos', $properties['updated_at_precise']['format']);
    }
    
    public function testRangeTypes(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        // IntegerRange
        $this->assertArrayHasKey('age_range', $properties);
        $this->assertEquals('integer_range', $properties['age_range']['type']);
        
        // FloatRange
        $this->assertArrayHasKey('score_range', $properties);
        $this->assertEquals('float_range', $properties['score_range']['type']);
        
        // LongRange
        $this->assertArrayHasKey('id_range', $properties);
        $this->assertEquals('long_range', $properties['id_range']['type']);
        
        // DoubleRange
        $this->assertArrayHasKey('precise_range', $properties);
        $this->assertEquals('double_range', $properties['precise_range']['type']);
        
        // DateRange
        $this->assertArrayHasKey('date_range', $properties);
        $this->assertEquals('date_range', $properties['date_range']['type']);
        
        // IpRange
        $this->assertArrayHasKey('network_range', $properties);
        $this->assertEquals('ip_range', $properties['network_range']['type']);
    }
    
    public function testNetworkTypes(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        // IP
        $this->assertArrayHasKey('ip_address', $properties);
        $this->assertEquals('ip', $properties['ip_address']['type']);
        $this->assertTrue($properties['ip_address']['ignore_malformed']);
        $this->assertEquals('0.0.0.0', $properties['ip_address']['null_value']);
    }
    
    public function testGeoTypes(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        // GeoPoint
        $this->assertArrayHasKey('location', $properties);
        $this->assertEquals('geo_point', $properties['location']['type']);
        $this->assertTrue($properties['location']['ignore_malformed']);
        $this->assertTrue($properties['location']['ignore_z_value']);
        
        // GeoShape
        $this->assertArrayHasKey('area', $properties);
        $this->assertEquals('geo_shape', $properties['area']['type']);
        $this->assertTrue($properties['area']['ignore_malformed']);
        $this->assertTrue($properties['area']['ignore_z_value']);
        $this->assertEquals('ccw', $properties['area']['orientation']);
        
        // Shape
        $this->assertArrayHasKey('shape', $properties);
        $this->assertEquals('shape', $properties['shape']['type']);
        
        // Point
        $this->assertArrayHasKey('point', $properties);
        $this->assertEquals('point', $properties['point']['type']);
    }
    
    public function testVectorTypes(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        // DenseVector
        $this->assertArrayHasKey('embedding', $properties);
        $this->assertEquals('dense_vector', $properties['embedding']['type']);
        $this->assertEquals('cosine', $properties['embedding']['similarity']);
        $this->assertEquals(128, $properties['embedding']['dims']);
        
        // SparseVector
        $this->assertArrayHasKey('sparse_embedding', $properties);
        $this->assertEquals('sparse_vector', $properties['sparse_embedding']['type']);
        
        // RankFeature
        $this->assertArrayHasKey('popularity', $properties);
        $this->assertEquals('rank_feature', $properties['popularity']['type']);
        $this->assertTrue($properties['popularity']['positive_score_impact']);
        
        // RankFeatures
        $this->assertArrayHasKey('keywords_rank', $properties);
        $this->assertEquals('rank_features', $properties['keywords_rank']['type']);
    }
    
    public function testSpecializedTypes(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        // Binary
        $this->assertArrayHasKey('binary_data', $properties);
        $this->assertEquals('binary', $properties['binary_data']['type']);
        
        // Completion
        $this->assertArrayHasKey('suggest', $properties);
        $this->assertEquals('completion', $properties['suggest']['type']);
        $this->assertEquals('custom_analyzer', $properties['suggest']['analyzer']);
        $this->assertTrue($properties['suggest']['preserve_separators']);
        $this->assertTrue($properties['suggest']['preserve_position_increments']);
        $this->assertEquals(50, $properties['suggest']['max_input_length']);
        
        // Version
        $this->assertArrayHasKey('doc_version', $properties);
        $this->assertEquals('version', $properties['doc_version']['type']);
        
        // Percolator
        $this->assertArrayHasKey('query', $properties);
        $this->assertEquals('percolator', $properties['query']['type']);
        
        // Boolean
        $this->assertArrayHasKey('is_active', $properties);
        $this->assertEquals('boolean', $properties['is_active']['type']);
        
        // Alias
        $this->assertArrayHasKey('id_alias', $properties);
        $this->assertEquals('alias', $properties['id_alias']['type']);
        $this->assertEquals('user_id', $properties['id_alias']['path']);
        
        // AggregateMetricDouble
        $this->assertArrayHasKey('stats', $properties);
        $this->assertEquals('aggregate_metric_double', $properties['stats']['type']);
        $this->assertContains('min', $properties['stats']['metrics']);
        $this->assertContains('max', $properties['stats']['metrics']);
        $this->assertEquals('max', $properties['stats']['default_metric']);
        
        // SemanticText
        $this->assertArrayHasKey('semantic_content', $properties);
        $this->assertEquals('semantic_text', $properties['semantic_content']['type']);
        
        // SearchAsYouType
        $this->assertArrayHasKey('quick_search', $properties);
        $this->assertEquals('search_as_you_type', $properties['quick_search']['type']);
        $this->assertEquals(3, $properties['quick_search']['max_shingle_size']);
    }
    
    public function testNestedType(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        // Nested
        $this->assertArrayHasKey('addresses', $properties);
        $this->assertEquals('nested', $properties['addresses']['type']);
        $this->assertArrayHasKey('properties', $properties['addresses']);
        
        // Verificar campos dentro do tipo aninhado
        $nestedProps = $properties['addresses']['properties'];
        $this->assertArrayHasKey('street', $nestedProps);
        $this->assertEquals('text', $nestedProps['street']['type']);
        $this->assertArrayHasKey('city', $nestedProps);
        $this->assertEquals('keyword', $nestedProps['city']['type']);
        $this->assertArrayHasKey('country', $nestedProps);
        $this->assertEquals('keyword', $nestedProps['country']['type']);
        $this->assertArrayHasKey('coordinates', $nestedProps);
        $this->assertEquals('geo_point', $nestedProps['coordinates']['type']);
    }
    
    public function testObjectType(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        // Object
        $this->assertArrayHasKey('contact', $properties);
        $this->assertArrayHasKey('properties', $properties['contact']);
        
        // Verificar campos dentro do tipo objeto
        $objectProps = $properties['contact']['properties'];
        $this->assertArrayHasKey('email', $objectProps);
        $this->assertEquals('keyword', $objectProps['email']['type']);
        $this->assertArrayHasKey('phone', $objectProps);
        $this->assertEquals('keyword', $objectProps['phone']['type']);
        $this->assertArrayHasKey('is_primary', $objectProps);
        $this->assertEquals('boolean', $objectProps['is_primary']['type']);
        
        // Verificar objeto aninhado dentro do objeto
        $this->assertArrayHasKey('social_media', $objectProps);
        $this->assertArrayHasKey('properties', $objectProps['social_media']);
        
        $socialProps = $objectProps['social_media']['properties'];
        $this->assertArrayHasKey('platform', $socialProps);
        $this->assertEquals('keyword', $socialProps['platform']['type']);
        $this->assertArrayHasKey('username', $socialProps);
        $this->assertEquals('keyword', $socialProps['username']['type']);
        $this->assertArrayHasKey('url', $socialProps);
        $this->assertEquals('keyword', $socialProps['url']['type']);
    }
    
    public function testAllFieldsArePresent(): void
    {
        $properties = $this->mapping->generateMapping()['properties'];
        
        // Lista de todos os campos que devem estar presentes
        $expectedFields = [
            'description', 'code', 'age', 'user_id', 'score', 'precise_score',
            'approximate_value', 'price', 'positive_number', 'created_at',
            'updated_at_precise', 'age_range', 'score_range', 'id_range',
            'precise_range', 'date_range', 'network_range', 'ip_address',
            'location', 'area', 'shape', 'point', 'embedding', 'sparse_embedding',
            'popularity', 'keywords_rank', 'binary_data', 'suggest', 'doc_version',
            'query', 'is_active', 'id_alias', 'stats', 'response_times',
            'semantic_content', 'quick_search', 'addresses', 'contact'
        ];
        
        foreach ($expectedFields as $field) {
            $this->assertArrayHasKey($field, $properties, "Campo {$field} não encontrado no mapeamento");
        }
    }
}
