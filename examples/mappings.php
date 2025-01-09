<?php

require_once __DIR__ . '/../vendor/autoload.php';


$index = new \Jot\HfElastic\Migration\Mapping('test_name');

$index->keyword('id');
$index->keyword('name')->normalizer('normalizer_ascii_lower');
$index->date('birth_date');
$index->keyword('phone_number');
$index->keyword('email');
$index->integer('height')->meta(['cm', 'm', 'ft']);

$preferences = new \Jot\HfElastic\Migration\ElasticsearchType\Nested('preferences');
$preferences->keyword('id');
$preferences->keyword('name')->normalizer('normalizer_ascii_lower');
$preferences->keyword('value');
$index->nested($preferences);

$index->date('created_at');
$index->date('updated_at');
$index->boolean('removed');

$address = new \Jot\HfElastic\Migration\ElasticsearchType\ObjectType('address');
$address->keyword('name')->normalizer('normalizer_ascii_lower');
$address->keyword('street');
$address->keyword('number');
$address->keyword('complement');
$address->keyword('neighborhood');
$index->child($address);

$city = new \Jot\HfElastic\Migration\ElasticsearchType\ObjectType('city');
$city->keyword('id');
$city->keyword('name');
$address->child($city);

$state = new \Jot\HfElastic\Migration\ElasticsearchType\ObjectType('state');
$state->keyword('id');
$state->keyword('name');
$city->child($state);

$index->child($address);

$index->settings([
    'index' => [
        'number_of_shards' => 3,
        'number_of_replicas' => 1,
    ],
    "analysis" => [
        "normalizer" => [
            "normalizer_ascii_lower" => [
                "type" => "custom",
                "char_filter" => [],
                "filter" => [
                    "asciifolding",
                    "lowercase"
                ]
            ]
        ]
    ]
]);

echo json_encode($index->body(), JSON_PRETTY_PRINT);