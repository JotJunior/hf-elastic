<?php

declare(strict_types=1);

namespace Jot\HfElasticMigrations;

/**
 * ConfigProvider for the jot/hf-elastic-migrations package.
 */
class ConfigProvider
{
    /**
     * Return the container definitions.
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                // Define your dependencies here
            ],
            'commands' => [
                // Define your commands here
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for jot/hf-elastic-migrations.',
                    'source' => __DIR__ . '/../publish/hf_elastic_migrations.php',
                    'destination' => BASE_PATH . '/config/autoload/hf_elastic_migrations.php',
                ],
            ],
        ];
    }
}
