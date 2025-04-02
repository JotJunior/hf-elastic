<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
use function Hyperf\Translation\__;

#[Command]
class MigrateCommand extends AbstractCommand
{
    /**
     * MigrateCommand constructor.
     * @param ContainerInterface $container The container instance.
     */
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct($container, 'elastic:migrate');
        $this->configure();
    }

    /**
     * Configure the command.
     * @return void
     */
    public function configure(): void
    {
        $this->setDescription('Create elasticsearch indices from migrations.');
        $this->addOption('index', 'I', InputOption::VALUE_OPTIONAL, 'Migrate all migration files for a specific index.');
        $this->addOption('file', 'F', InputOption::VALUE_OPTIONAL, 'Migrate a specific migration file.');
    }

    /**
     * Handle the command execution.
     * @return int
     */
    public function handle()
    {
        if (!$this->migrationDirectoryExists()) {
            return 1;
        }

        $index = $this->input->getOption('index');
        $migrationFile = $this->input->getOption('file');
        $migrations = $this->getMigrationFiles($index, $migrationFile);

        if (empty($migrations)) {
            $this->line(__('hf-elastic.no_migration'));
        }

        foreach ($migrations as $migration) {
            try {
                $migration->up();
                $this->line(__('hf-elastic.index_created', ['index' => $migration::INDEX_NAME]));
            } catch (\Throwable $e) {
                $this->line(sprintf('<fg=yellow>[SKIP]</> %s.', $e->getMessage()));
            }
        }

        return 0;
    }
}
