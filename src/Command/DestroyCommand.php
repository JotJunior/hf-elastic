<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class DestroyCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('elastic:destroy');
        $this->setDescription('Remove all indices.');
        $this->addOption('index', 'I', InputOption::VALUE_REQUIRED, 'The index name.');
    }


    public function handle()
    {

        $this->line('<fg=yellow>WARNING :: WARNING :: WARNING</>');
        $this->line('This command will remove all indices. The operation cannot be undone and all data will be lost.');
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
            $migration->delete($migration::INDEX_NAME);
            $this->line(sprintf('<fg=green>[OK]</> Index <fg=yellow>%s</> removed.', $migration::INDEX_NAME));
        }
    }
}
