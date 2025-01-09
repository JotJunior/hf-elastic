<?php

use Jot\HfElastic\Migration;
use Jot\HfElastic\Migration\ElasticsearchType\Nested;
use Jot\HfElastic\Migration\ElasticsearchType\ObjectType;
use Jot\HfElastic\Migration\Mapping;

return new class extends Migration {

    public const INDEX_NAME = 'users';

    public function up(): void
    {
        $index = new Mapping(name: self::INDEX_NAME);

        $index->keyword('id');
        $index->keyword('name')->normalizer('normalizer_ascii_lower');
        $index->date('birth_date');
        $index->keyword('phone_number');
        $index->keyword('email');

        $preferences = new Nested('preferences');
        $preferences->keyword('id');
        $preferences->keyword('name')->normalizer('normalizer_ascii_lower');
        $preferences->keyword('value');
        $index->nested($preferences);

        $index->date('created_at');
        $index->date('updated_at');
        $index->boolean('removed');

        $address = new ObjectType('address');
        $address->keyword('name')->normalizer('normalizer_ascii_lower');
        $address->keyword('street');
        $address->keyword('number');
        $address->keyword('complement');
        $address->keyword('neighborhood');
        $index->child($address);

        $city = new ObjectType('city');
        $city->keyword('id');
        $city->keyword('name');
        $address->child($city);

        $state = new ObjectType('state');
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

        $this->create($index);

    }

    public function down(): void
    {
        $this->delete();
    }
};