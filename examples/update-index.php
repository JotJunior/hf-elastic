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

        $this->update($index);

    }
};