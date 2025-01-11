<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Elasticsearch\Client;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Jot\HfElastic\ClientBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class ResetCommand extends HyperfCommand
{
    protected Client $esClient;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('elastic:reset');
        $this->setDescription('Remove and create all indices.');
        $this->addOption('index', 'I', InputOption::VALUE_REQUIRED, 'The index name.');
        $this->esClient = $this->container->get(ClientBuilder::class)->build();
    }


    public function handle()
    {

        $this->line('<fg=yellow>WARNING :: WARNING :: WARNING</>');
        $this->line('This command will remove and re-create all indices. The operation cannot be undone and all data will be lost.');
        $this->newLine();
        $answer = $this->ask('Are you sure you want to remove all indices? [y/N]', 'N');
        if ($answer !== 'y') {
            $this->line('Aborted.');
            return;
        }

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
            $migration->delete($migration::INDEX_NAME);
            $this->line(sprintf('<fg=green>[OK]</> Index <fg=yellow>%s</> removed.', $migration::INDEX_NAME));
            $migration->up();
            $this->line(sprintf('<fg=green>[OK]</> Index <fg=yellow>%s</> created.', $migration::INDEX_NAME));
        }
    }
}
