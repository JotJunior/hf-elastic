<?php

namespace Jot\HfElastic\Tests\Unit\Migration;

use Jot\HfElastic\Migration\ElasticType;
use Jot\HfElastic\Migration\Mapping;
use PHPUnit\Framework\TestCase;

class ElasticTypeOptionsTest extends TestCase
{
    public function testTextTypeOptions(): void
    {
        $field = new ElasticType\TextType('description');
        $field->analyzer('standard')
            ->eagerGlobalOrdinals(true)
            ->fielddata(true)
            ->index(true)
            ->indexOptions('positions')
            ->norms(false)
            ->store(true)
            ->searchAnalyzer('english')
            ->similarity('BM25');

        $options = $field->getOptions();

        $this->assertEquals('standard', $options['analyzer']);
        $this->assertTrue($options['eager_global_ordinals']);
        $this->assertTrue($options['fielddata']);
        $this->assertTrue($options['index']);
        $this->assertEquals('positions', $options['index_options']);
        $this->assertFalse($options['norms']);
        $this->assertTrue($options['store']);
        $this->assertEquals('english', $options['search_analyzer']);
        $this->assertEquals('BM25', $options['similarity']);
    }

    public function testKeywordTypeOptions(): void
    {
        $field = new ElasticType\KeywordType('code');
        $field->docValues(true)
            ->eagerGlobalOrdinals(true)
            ->ignoreAbove(256)
            ->index(true)
            ->indexOptions('docs')
            ->norms(false)
            ->nullValue('N/A')
            ->store(true)
            ->normalizer('lowercase')
            ->splitQueriesOnWhitespace(true);

        $options = $field->getOptions();

        $this->assertTrue($options['doc_values']);
        $this->assertTrue($options['eager_global_ordinals']);
        $this->assertEquals(256, $options['ignore_above']);
        $this->assertTrue($options['index']);
        $this->assertEquals('docs', $options['index_options']);
        $this->assertFalse($options['norms']);
        $this->assertEquals('N/A', $options['null_value']);
        $this->assertTrue($options['store']);
        $this->assertEquals('lowercase', $options['normalizer']);
        $this->assertTrue($options['split_queries_on_whitespace']);
    }

    public function testNumericTypeOptions(): void
    {
        $field = new ElasticType\IntegerType('age');
        $field->coerce(true)
            ->ignoreMalformed(true)
            ->index(true)
            ->nullValue(0)
            ->store(true);

        $options = $field->getOptions();

        $this->assertTrue($options['coerce']);
        $this->assertTrue($options['ignore_malformed']);
        $this->assertTrue($options['index']);
        $this->assertEquals(0, $options['null_value']);
        $this->assertTrue($options['store']);
    }

    public function testDateTypeOptions(): void
    {
        $field = new ElasticType\DateType('created_at');
        $field->format('yyyy-MM-dd')
            ->ignoreMalformed(true)
            ->nullValue('1970-01-01');

        $options = $field->getOptions();

        $this->assertEquals('yyyy-MM-dd', $options['format']);
        $this->assertTrue($options['ignore_malformed']);
        $this->assertEquals('1970-01-01', $options['null_value']);
    }

    public function testGeoPointTypeOptions(): void
    {
        $field = new ElasticType\GeoPointType('location');
        $field->ignoreMalformed(true)
            ->ignoreZValue(true)
            ->index(true)
            ->nullValue('POINT (0 0)');

        $options = $field->getOptions();

        $this->assertTrue($options['ignore_malformed']);
        $this->assertTrue($options['ignore_z_value']);
        $this->assertTrue($options['index']);
        $this->assertEquals('POINT (0 0)', $options['null_value']);
    }

    public function testDenseVectorTypeOptions(): void
    {
        $field = new ElasticType\DenseVectorType('embedding', 128);
        $field->similarity('cosine');

        $options = $field->getOptions();

        $this->assertEquals(128, $options['dims']);
        $this->assertEquals('cosine', $options['similarity']);
    }

    public function testNestedTypeWithFields(): void
    {
        $nested = new ElasticType\NestedType('addresses');
        $nested->text('street')->analyzer('standard');
        $nested->keyword('city')->normalizer('lowercase');
        $nested->addField('geo_point', 'coordinates')->ignoreMalformed(true);

        // Verificar se os campos foram adicionados corretamente
        $properties = $nested->getProperties();

        $this->assertArrayHasKey('street', $properties);
        $this->assertEquals('text', $properties['street']['type']);
        $this->assertEquals('standard', $properties['street']['analyzer']);

        $this->assertArrayHasKey('city', $properties);
        $this->assertEquals('keyword', $properties['city']['type']);
        $this->assertEquals('lowercase', $properties['city']['normalizer']);

        $this->assertArrayHasKey('coordinates', $properties);
        $this->assertEquals('geo_point', $properties['coordinates']['type']);
        $this->assertTrue($properties['coordinates']['ignore_malformed']);
    }

    public function testObjectTypeWithNestedObjects(): void
    {
        $object = new ElasticType\ObjectType('contact');
        $object->keyword('email')->ignoreAbove(100);
        $object->keyword('phone')->ignoreAbove(20);

        // Adicionar um objeto aninhado
        $socialMedia = new ElasticType\ObjectType('social_media');
        $socialMedia->keyword('platform');
        $socialMedia->keyword('username');
        $object->object($socialMedia);

        // Verificar se os campos foram adicionados corretamente
        $properties = $object->getProperties();

        $this->assertArrayHasKey('email', $properties);
        $this->assertEquals('keyword', $properties['email']['type']);
        $this->assertEquals(100, $properties['email']['ignore_above']);

        $this->assertArrayHasKey('phone', $properties);
        $this->assertEquals('keyword', $properties['phone']['type']);
        $this->assertEquals(20, $properties['phone']['ignore_above']);

        // Verificar objeto aninhado
        $this->assertArrayHasKey('social_media', $properties);
        $this->assertArrayHasKey('properties', $properties['social_media']);
        $this->assertArrayHasKey('platform', $properties['social_media']['properties']);
        $this->assertEquals('keyword', $properties['social_media']['properties']['platform']['type']);
        $this->assertArrayHasKey('username', $properties['social_media']['properties']);
        $this->assertEquals('keyword', $properties['social_media']['properties']['username']['type']);
    }

    public function testCreateCompleteMapping(): void
    {
        $mapping = new Mapping('test-index');

        // Adicionar configurações
        $mapping->settings([
            'number_of_shards' => 3,
            'number_of_replicas' => 2
        ]);

        // Adicionar campos de diferentes tipos
        $mapping->text('description')->analyzer('standard');
        $mapping->keyword('code')->ignoreAbove(256);
        $mapping->addField('integer', 'age')->nullValue(0);
        $mapping->date('created_at')->format('yyyy-MM-dd');
        $mapping->addField('geo_point', 'location');

        // Adicionar um campo aninhado
        $nested = new ElasticType\NestedType('addresses');
        $nested->text('street');
        $nested->keyword('city');
        $mapping->nested($nested);

        // Adicionar um campo de objeto
        $object = new ElasticType\ObjectType('metadata');
        $object->keyword('category');
        $object->date('published_at');
        $mapping->object($object);

        // Verificar o mapeamento gerado
        $generatedMapping = $mapping->generateMapping();

        $this->assertArrayHasKey('properties', $generatedMapping);
        $this->assertCount(7, $generatedMapping['properties']);

        // Verificar campos básicos
        $this->assertArrayHasKey('description', $generatedMapping['properties']);
        $this->assertArrayHasKey('code', $generatedMapping['properties']);
        $this->assertArrayHasKey('age', $generatedMapping['properties']);
        $this->assertArrayHasKey('created_at', $generatedMapping['properties']);
        $this->assertArrayHasKey('location', $generatedMapping['properties']);

        // Verificar campos complexos
        $this->assertArrayHasKey('addresses', $generatedMapping['properties']);
        $this->assertArrayHasKey('metadata', $generatedMapping['properties']);

        // Verificar configurações
        $body = $mapping->body();
        $this->assertEquals(3, $body['body']['settings']['number_of_shards']);
        $this->assertEquals(2, $body['body']['settings']['number_of_replicas']);
    }
}
