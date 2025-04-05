<?php

declare(strict_types=1);

namespace Jot\HfElasticCore;

/**
 * ConfigProvider for the jot/hf-elastic-core package.
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
                    'description' => 'The config for jot/hf-elastic-core.',
                    'source' => __DIR__ . '/../publish/hf_elastic_core.php',
                    'destination' => BASE_PATH . '/config/autoload/hf_elastic_core.php',
                ],
            ],
        ];
    }
}
