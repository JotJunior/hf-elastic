<?php

namespace Jot\HfElastic;

use Hyperf\Etcd\Client as EtcdClient;
use Hyperf\Elasticsearch\ClientBuilderFactory;
use Jot\HfElastic\Command\MigrateCommand;
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
                MigrateCommand::class
            ],
            'listeners' => [],
            'publish' => [],
        ];
    }
}