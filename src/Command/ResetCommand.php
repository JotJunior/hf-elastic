<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

#[Command]
class ResetCommand extends AbstractCommand
{
    protected ConfigInterface $config;

    /**
     * ResetCommand constructor.
     * @param ContainerInterface $container the container instance
     */
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct($container, 'elastic:reset');
        $this->config = $this->container->get(ConfigInterface::class);
        $this->configure();
    }

    /**
     * Configure the command.
     */
    public function configure(): void
    {
        $this->setDescription('Remove and create all indices.');
        $this->addOption('index', 'I', InputOption::VALUE_REQUIRED, 'The index name.');
        $this->addOption('file', 'F', InputOption::VALUE_OPTIONAL, 'Destroy the index migration file for a specific index.');
    }

    /**
     * Handle the command execution.
     * @return int
     */
    public function handle()
    {
        $this->line('<fg=yellow>WARNING :: WARNING :: WARNING</>');
        $this->line('This command will remove and re-create all indices. The operation cannot be undone and all data will be lost.');
        $this->newLine();

        $answer = $this->ask('Are you sure you want to remove all indices? [y/N]', 'N');
        if ($answer !== 'y') {
            $this->line('Aborted.');
            return 0;
        }

        if (! $this->migrationDirectoryExists()) {
            return 1;
        }

        $index = $this->input->getOption('index');
        $migrationFile = $this->input->getOption('file');
        $migrations = $this->getMigrationFiles($index, $migrationFile);

        if (empty($migrations)) {
            $this->line('<fg=yellow>[INFO]</> No migrations found to process.');
        }

        foreach ($migrations as $migration) {
            try {
                $indexName = $migration::INDEX_NAME;
                if ($migration->addPrefix) {
                    $indexName = sprintf('%s_%s', $this->config->get('hf_elastic.prefix'), $indexName);
                }

                // Delete the index
                $migration->delete($indexName);
                $this->line(sprintf('<fg=green>[OK]</> Index <fg=yellow>%s</> removed.', $indexName));

                // Recreate the index
                $migration->up();
                $this->line(sprintf('<fg=green>[OK]</> Index <fg=yellow>%s</> created.', $indexName));
            } catch (Throwable $e) {
                $this->line(sprintf('<fg=red>[ERROR]</> Failed to reset index %s: %s', $indexName ?? $migration::INDEX_NAME, $e->getMessage()));
            }
        }

        return 0;
    }
}
