<?php

namespace Jot\HfElastic;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Jot\HfElastic\Command\DestroyCommand;
use Jot\HfElastic\Command\MigrateCommand;
use Jot\HfElastic\Command\MigrationCommand;
use Jot\HfElastic\Command\ResetCommand;
use Jot\HfElastic\Query\ElasticQueryBuilder;
use Jot\HfElastic\Query\OperatorRegistry;
use Jot\HfElastic\Query\Operators\EqualsOperator;
use Jot\HfElastic\Query\Operators\NotEqualsOperator;
use Jot\HfElastic\Query\Operators\RangeOperator;
use Jot\HfElastic\Query\QueryContext;
use Jot\HfElastic\Services\IndexNameFormatter;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                Contracts\MigrationInterface::class => Migration::class,
                Contracts\QueryBuilderInterface::class => function (ContainerInterface $container) {
                    return new ElasticQueryBuilder(
                        clientBuilder: $container->get(ClientBuilder::class),
                        indexFormatter: $container->get(IndexNameFormatter::class),
                        operatorRegistry: $container->get(OperatorRegistry::class),
                        queryContext: $container->get(QueryContext::class),
                    );
                },
                IndexNameFormatter::class => function (ContainerInterface $container) {
                    $config = $container->get(ConfigInterface::class);
                    return new IndexNameFormatter($config->get('hf_elastic.prefix', ''));
                },
                OperatorRegistry::class => function () {
                    $registry = new OperatorRegistry();
                    $registry->register(new EqualsOperator());
                    $registry->register(new NotEqualsOperator());
                    $registry->register(new RangeOperator());
                    return $registry;
                },
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
                [
                    'id' => 'translation-en',
                    'description' => 'The english translation files for hf-elastic package.',
                    'source' => __DIR__ . '/../storage/languages/en/hf-elastic.php',
                    'destination' => BASE_PATH . '/storage/languages/en/hf-elastic.php',
                    'merge' => true
                ],
                [
                    'id' => 'translation-pt_BR',
                    'description' => 'The brazilian portuguese translation files for hf-elastic package.',
                    'source' => __DIR__ . '/../storage/languages/pt_BR/hf-elastic.php',
                    'destination' => BASE_PATH . '/storage/languages/pt_BR/hf-elastic.php',
                    'merge' => true
                ],
            ],
        ];
    }
}
