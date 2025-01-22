<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class MigrateCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('elastic:migrate');
        $this->setDescription('Create elasticsearch indices from migrations.');
        $this->addOption('index', 'I', InputOption::VALUE_OPTIONAL, 'Migrate all migration files for a specific index.');
        $this->addOption('file', 'F', InputOption::VALUE_OPTIONAL, 'Migrate a specific migration file.');
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

            try {
                $migration->up();
                $this->line(sprintf('<fg=green>[OK]</> Index %s created.', $migration::INDEX_NAME));
            } catch (\Throwable $e) {
                $this->line(sprintf('<fg=yellow>[SKIP]</> %s.', $e->getMessage()));
            }
        }
    }
}
