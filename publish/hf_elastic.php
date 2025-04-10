<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */
use function Hyperf\Support\env;

return [
    'hosts' => explode(',', env('ELASTICSEARCH_HOSTS', 'http://localhost:9200')),
    'username' => env('ELASTICSEARCH_USERNAME', 'elastic'),
    'password' => env('ELASTICSEARCH_PASSWORD', ''),
    'api_id' => env('ELASTICSEARCH_API_ID', ''),
    'api_key' => env('ELASTICSEARCH_API_KEY', ''),
    'prefix' => env('ELASTICSEARCH_INDEX_PREFIX', ''),
    'dynamic' => env('ELASTICSEARCH_INDEX_DYNAMIC', 'strict'),
    'settings' => [
        'index' => [
            'number_of_shards' => (int) env('ELASTICSEARCH_SHARDS', 1),
            'number_of_replicas' => (int) env('ELASTICSEARCH_REPLICAS', 1),
            'refresh_interval' => '1s',
        ],
        'analysis' => [
            'normalizer' => [
                'normalizer_ascii_lower' => [
                    'type' => 'custom',
                    'char_filter' => [],
                    'filter' => [
                        'asciifolding',
                        'lowercase',
                    ],
                ],
            ],
        ],
    ],
];
