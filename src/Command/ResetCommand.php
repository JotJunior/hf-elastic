<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class ResetCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('elastic:reset');
        $this->setDescription('Remove and create all indices.');
        $this->addOption('index', 'I', InputOption::VALUE_REQUIRED, 'The index name.');
        $this->addOption('index', 'I', InputOption::VALUE_OPTIONAL, 'Destroy all migration files for a specific index.');
        $this->addOption('file', 'F', InputOption::VALUE_OPTIONAL, 'Destroy the index migration file for a specific index.');
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

        $index = $this->input->getOption('index');
        $migrationFile = $this->input->getOption('file');

        foreach (glob($migrationDirectory . '/*.php') as $file) {

            $migration = include $file;

            if ($index && $migration::INDEX_NAME !== $index) {
                continue;
            }

            if ($migrationFile && $migrationFile !== basename($file)) {
                continue;
            }

            $migration->delete($migration::INDEX_NAME);
            $this->line(sprintf('<fg=green>[OK]</> Index <fg=yellow>%s</> removed.', $migration::INDEX_NAME));
            $migration->up();
            $this->line(sprintf('<fg=green>[OK]</> Index <fg=yellow>%s</> created.', $migration::INDEX_NAME));
        }
    }
}
