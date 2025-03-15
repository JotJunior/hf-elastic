<?php

namespace Jot\HfElastic;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Jot\HfElastic\Command\DestroyCommand;
use Jot\HfElastic\Command\MigrateCommand;
use Jot\HfElastic\Command\MigrationCommand;
use Jot\HfElastic\Command\ResetCommand;
use Jot\HfElastic\Contracts\QueryBuilderInterface;
use Jot\HfElastic\Provider\ElasticServiceProvider;
use Jot\HfElastic\Query\ElasticQueryBuilder;
use Jot\HfElastic\Query\OperatorRegistry;
use Jot\HfElastic\Query\QueryContext;
use Jot\HfElastic\Services\IndexNameFormatter;
use function Hyperf\Support\make;

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
                QueryBuilderInterface::class => function (ContainerInterface $container) {
                    return new ElasticQueryBuilder(
                        client: make(ClientBuilder::class)->build(),
                        indexFormatter: make(IndexNameFormatter::class, ['prefix' => $container->get(ConfigInterface::class)->get('hf_elastic.prefix', '')]),
                        operatorRegistry: make(OperatorRegistry::class),
                        queryContext: make(QueryContext::class),
                    );
                }
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
