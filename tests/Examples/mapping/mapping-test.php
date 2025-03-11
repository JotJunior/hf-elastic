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
$index->integer('age')
    ->coerce(true)
    ->ignoreMalformed(true)
    ->index(true)
    ->nullValue(0)
    ->store(true);

$index->long('user_id')
    ->coerce(true)
    ->index(true)
    ->nullValue(0);

$index->float('score')
    ->coerce(true)
    ->ignoreMalformed(true)
    ->nullValue(0.0);

$index->double('precise_score')
    ->coerce(true)
    ->ignoreMalformed(true);

$index->halfFloat('approximate_value')
    ->coerce(true);

$index->scaledFloat('price')
    ->scalingFactor(100);

$index->unsignedLong('positive_number')
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
$index->integerRange('age_range')
    ->coerce(true)
    ->index(true);

$index->floatRange('score_range')
    ->index(true);

$index->longRange('id_range')
    ->index(true);

$index->doubleRange('precise_range')
    ->index(true);

$index->dateRange('date_range')
    ->format('yyyy-MM-dd')
    ->index(true);

$index->ipRange('network_range')
    ->index(true);

// 5. Tipos de rede
$index->ip('ip_address')
    ->ignoreMalformed(true)
    ->index(true)
    ->nullValue('0.0.0.0');

// 6. Tipos geoespaciais
$index->geoPoint('location')
    ->ignoreMalformed(true)
    ->ignoreZValue(true)
    ->nullValue('POINT (0 0)');

$index->geoShape('area')
    ->ignoreMalformed(true)
    ->ignoreZValue(true)
    ->orientation('ccw');

$index->shape('shape')
    ->ignoreMalformed(true)
    ->ignoreZValue(true);

$index->point('point')
    ->ignoreMalformed(true)
    ->ignoreZValue(true);

// 7. Tipos para vetores e machine learning
$index->denseVector('embedding', 128)
    ->similarity('cosine');

$index->sparseVector('sparse_embedding');

$index->rankFeature('popularity')
    ->positiveScoreImpact(true);

$index->rankFeatures('keywords_rank');

// 8. Tipos especializados
$index->binary('binary_data')
    ->docValues(true)
    ->store(true);

$index->completion('suggest')
    ->analyzer('custom_analyzer')
    ->preserveSeparators(true)
    ->preservePositionIncrements(true)
    ->maxInputLength(50);

$index->version('doc_version');

$index->percolator('query');

$index->boolean('is_active')
    ->index(true)
    ->nullValue(false);

$index->alias('id_alias')
    ->path('user_id');

$index->aggregateMetricDouble('stats', ['min', 'max', 'sum', 'value_count'])
    ->defaultMetric('max');

$index->histogram('response_times')
    ->ignoreMalformed(true);

$index->semanticText('semantic_content')
    ->modelId('sentence-transformers__all-MiniLM-L6-v2')
    ->dimensions(384);

$index->searchAsYouType('quick_search')
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
$nested->geoPoint('coordinates')
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
