<?php

namespace Jot\HfElastic;

use Hyperf\Elasticsearch\ClientBuilderFactory;
use Hyperf\Etcd\KVInterface;
use Jot\HfElastic\Command\DestroyCommand;
use Jot\HfElastic\Command\MigrateCommand;
use Jot\HfElastic\Command\MigrationCommand;
use Jot\HfElastic\Command\ResetCommand;
use Psr\Container\ContainerInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                ClientBuilder::class => function (ContainerInterface $container) {
                    return new ClientBuilder(
                        $container->get(KVInterface::class),
                        $container->get(ClientBuilderFactory::class)
                    );
                },
            ],
            'commands' => [
                DestroyCommand::class,
                MigrateCommand::class,
                MigrationCommand::class,
                ResetCommand::class,
            ],
            'listeners' => [],
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