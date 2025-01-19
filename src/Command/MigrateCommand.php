<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Elasticsearch\Client;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class MigrateCommand extends HyperfCommand
{
    protected Client $esClient;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('elastic:migrate');
        $this->setDescription('Create elasticsearch indices from migrations.');
        $this->addOption('index', 'I', InputOption::VALUE_REQUIRED, 'The index name.');
        $this->esClient = $container->get(\Jot\HfElastic\ClientBuilder::class)->build();
    }

    public function handle()
    {

        if (!defined('BASE_PATH')) {
            define('BASE_PATH', \dirname(__DIR__, 4));
        }
        $migrationDirectory = BASE_PATH . '/migrations/elasticsearch';

        if (!is_dir($migrationDirectory)) {
            $this->line(sprintf('<fg=red>[ERROR]</> Missing migration directory %s', $migrationDirectory));
            return;
        }

        foreach (glob($migrationDirectory . '/*.php') as $file) {
            $migration = include $file;
            $migration->setClient($this->esClient);
            try {
                $migration->up();
                $this->line(sprintf('<fg=green>[OK]</> Index %s created.', $migration::INDEX_NAME));
            } catch (\Throwable $e) {
                $this->line(sprintf('<fg=yellow>[SKIP]</> %s.', $e->getMessage()));
            }
        }
    }
}
