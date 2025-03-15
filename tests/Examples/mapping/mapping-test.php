<?php

use Jot\HfElastic\Migration\ElasticType;
use Jot\HfElastic\Migration\Mapping;

// Criar um novo índice com todos os tipos disponíveis no Elasticsearch
$index = new Mapping('complete-test-index');
$index->settings([
    'number_of_shards' => 3,
    'number_of_replicas' => 2,
    'refresh_interval' => '1s',
    'analysis' => [
        'analyzer' => [
            'custom_analyzer' => [
                'type' => 'custom',
                'tokenizer' => 'standard',
                'filter' => ['lowercase', 'asciifolding']
            ]
        ],
        'normalizer' => [
            'my_normalizer' => [
                'type' => 'custom',
                'filter' => ['lowercase', 'asciifolding']
            ]
        ]
    ]
]);

// Tipos básicos
// 1. Texto e palavra-chave
$index->text('description')
    ->analyzer('custom_analyzer')
    ->eagerGlobalOrdinals(true)
    ->fielddata(true)
    ->index(true)
    ->indexOptions('positions')
    ->norms(false)
    ->store(true)
    ->searchAnalyzer('standard')
    ->similarity('BM25');

$index->keyword('code')
    ->docValues(true)
    ->eagerGlobalOrdinals(true)
    ->ignoreAbove(256)
    ->index(true)
    ->indexOptions('docs')
    ->norms(false)
    ->nullValue('N/A')
    ->store(true)
    ->normalizer('my_normalizer')
    ->splitQueriesOnWhitespace(true);

// 2. Tipos numéricos
$index->addField('integer', 'age')
    ->coerce(true)
    ->ignoreMalformed(true)
    ->index(true)
    ->nullValue(0)
    ->store(true);

$index->long('user_id')
    ->coerce(true)
    ->index(true)
    ->nullValue(0);

$index->addField('float', 'score')
    ->coerce(true)
    ->ignoreMalformed(true)
    ->nullValue(0.0);

$index->addField('double', 'precise_score')
    ->coerce(true)
    ->ignoreMalformed(true);

$index->addField('half_float', 'approximate_value')
    ->coerce(true);

$index->addField('scaled_float', 'price')
    ->scalingFactor(100);

$index->addField('unsigned_long', 'positive_number')
    ->nullValue('0');

// 3. Tipos de data e hora
$index->date('created_at')
    ->format('yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis')
    ->ignoreMalformed(true)
    ->nullValue('1970-01-01');

$index->dateNanos('updated_at_precise')
    ->format('yyyy-MM-dd HH:mm:ss.SSSSSS||strict_date_optional_time_nanos')
    ->ignoreMalformed(true);

// 4. Tipos de intervalo
$index->addField('integer_range', 'age_range')
    ->coerce(true)
    ->index(true);

$index->addField('float_range', 'score_range')
    ->index(true);

$index->addField('long_range', 'id_range')
    ->index(true);

$index->addField('double_range', 'precise_range')
    ->index(true);

$index->addField('date_range', 'date_range')
    ->format('yyyy-MM-dd')
    ->index(true);

$index->addField('ip_range', 'network_range')
    ->index(true);

// 5. Tipos de rede
$index->addField('ip', 'ip_address')
    ->ignoreMalformed(true)
    ->index(true)
    ->nullValue('0.0.0.0');

// 6. Tipos geoespaciais
$index->addField('geo_point', 'location')
    ->ignoreMalformed(true)
    ->ignoreZValue(true)
    ->nullValue('POINT (0 0)');

$index->addField('geo_shape', 'area')
    ->ignoreMalformed(true)
    ->ignoreZValue(true)
    ->orientation('ccw');

$index->addField('shape', 'shape')
    ->ignoreMalformed(true)
    ->ignoreZValue(true);

$index->addField('point', 'point')
    ->ignoreMalformed(true)
    ->ignoreZValue(true);

// 7. Tipos para vetores e machine learning
$index->addField('dense_vector', 'embedding', ['dims' => 128])
    ->similarity('cosine');

$index->addField('sparse_vector', 'sparse_embedding');

$index->addField('rank_feature', 'popularity')
    ->positiveScoreImpact(true);

$index->addField('rank_features', 'keywords_rank');

// 8. Tipos especializados
$index->addField('binary', 'binary_data')
    ->docValues(true)
    ->store(true);

$index->addField('completion', 'suggest')
    ->analyzer('custom_analyzer')
    ->preserveSeparators(true)
    ->preservePositionIncrements(true)
    ->maxInputLength(50);

$index->addField('version', 'doc_version');

$index->addField('percolator', 'query');

$index->boolean('is_active')
    ->index(true)
    ->nullValue(false);

$index->alias('id_alias')
    ->path('user_id');

$index->addField('aggregate_metric_double', 'stats')
    ->defaultMetric('max')
    ->metrics(['min', 'max', 'avg', 'sum', 'value_count']);

$index->addField('histogram', 'response_times')
    ->ignoreMalformed(true);

$index->addField('semantic_text', 'semantic_content')
    ->modelId('sentence-transformers__all-MiniLM-L6-v2')
    ->dimensions(384);

$index->addField('search_as_you_type', 'quick_search')
    ->analyzer('custom_analyzer')
    ->maxShingleSize(3);

// 9. Tipos complexos - Nested e Object
$nested = new ElasticType\NestedType('addresses');
$nested->text('street')
    ->analyzer('custom_analyzer');
$nested->keyword('city')
    ->normalizer('my_normalizer');
$nested->keyword('country')
    ->normalizer('my_normalizer');
$nested->addField('geo_point', 'coordinates')
    ->ignoreMalformed(true);
$index->nested($nested);

$object = new ElasticType\ObjectType('contact');
$object->keyword('email')
    ->ignoreAbove(100);
$object->keyword('phone')
    ->ignoreAbove(20);
$object->boolean('is_primary')
    ->nullValue(false);

// Objeto aninhado dentro de outro objeto
$socialMedia = new ElasticType\ObjectType('social_media');
$socialMedia->keyword('platform');
$socialMedia->keyword('username');
$socialMedia->keyword('url');
$object->object($socialMedia);

$index->object($object);

// Retornar o índice para uso
return $index;
