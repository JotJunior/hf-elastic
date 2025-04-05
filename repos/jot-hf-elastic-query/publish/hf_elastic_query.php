<?php

declare(strict_types=1);

return [
    'query_builder' => [
        'default_size' => 10,
        'max_size' => 10000,
        'operators' => [
            'equals' => \Jot\HfElasticQuery\Query\Operators\EqualsOperator::class,
            'not_equals' => \Jot\HfElasticQuery\Query\Operators\NotEqualsOperator::class,
            'range' => \Jot\HfElasticQuery\Query\Operators\RangeOperator::class,
        ],
    ],
];
