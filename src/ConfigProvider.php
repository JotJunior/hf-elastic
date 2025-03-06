<?php

namespace Jot\HfElastic;

use Jot\HfElastic\Command\DestroyCommand;
use Jot\HfElastic\Command\MigrateCommand;
use Jot\HfElastic\Command\MigrationCommand;
use Jot\HfElastic\Command\ResetCommand;
use Jot\HfElastic\Provider\ElasticServiceProvider;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                // Register our service provider to set up all dependencies
                'providers' => [
                    ElasticServiceProvider::class,
                ],
                // Interface bindings
                Contracts\MigrationInterface::class => Migration::class,
            ],
            'commands' => [
                DestroyCommand::class,
                MigrateCommand::class,
                MigrationCommand::class,
                ResetCommand::class,
            ],
            'listeners' => [],
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
                    'description' => 'The config of hf-elastic package.',
                    'source' => __DIR__ . '/../publish/hf_elastic.php',
                    'destination' => BASE_PATH . '/config/autoload/hf_elastic.php',
                ],
            ],
        ];
    }
}