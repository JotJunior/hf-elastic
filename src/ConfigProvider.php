<?php

namespace Jot\HfElastic;

use Hyperf\Etcd\KVInterface as EtcdClient;
use Hyperf\Elasticsearch\ClientBuilderFactory;
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
                ElasticsearchService::class => function (ContainerInterface $container) {
                    return new ElasticsearchService(
                        $container->get(EtcdClient::class),
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
            'publish' => [],
        ];
    }
}