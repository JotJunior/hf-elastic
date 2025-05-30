<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */
use Hyperf\Context\ApplicationContext;
use Jot\HfElastic\Migration;
use Jot\HfElastic\Migration\Mapping;

return new class(ApplicationContext::getContainer()) extends Migration {
    public const INDEX_NAME = 'clients';

    public bool $addPrefix = true;

    public function up(): void
    {
        $index = new Mapping(name: self::INDEX_NAME);

        $index->keyword('id');
        $index->keyword('name')->normalizer('normalizer_ascii_lower');
        $index->keyword('redirect_uri');
        $index->keyword('secret');
        $index->boolean('confidential');
        $index->keyword('status');

        // client attached tenant
        $tenant = new Migration\ElasticType\ObjectType('tenant');
        $tenant->keyword('id');
        $tenant->keyword('name');
        $index->object($tenant);

        // enabled scopes
        $scopes = new Migration\ElasticType\NestedType('scopes');
        $scopes->keyword('id');
        $scopes->keyword('name')->normalizer('normalizer_ascii_lower');
        $index->nested($scopes);

        $index->alias('client_identifier')->path('id');
        $index->alias('tenant_identifier')->path('tenant.id');
        $index->defaults();

        $index->settings([
            'index' => [
                'number_of_shards' => $this->settings['index']['number_of_shards'],
                'number_of_replicas' => $this->settings['index']['number_of_replicas'],
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
        ]);

        $this->create($index);
    }

    public function down(): void
    {
        $this->delete(self::INDEX_NAME);
    }
};
