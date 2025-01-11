<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Jot\HfElastic\ClientBuilder;
use Jot\HfElastic\Exception\MissingMigrationDirectoryException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class MigrationCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('elastic:migration');
        $this->setDescription('Create a new migration for Elasticsearch.');
        $this->addUsage('elastic:migration --index=index_name');
        $this->addUsage('elastic:migration --index=index_name --update');
        $this->addArgument('index', InputOption::VALUE_REQUIRED, 'The index name.');
        $this->addOption('update', 'U', InputOption::VALUE_NONE, 'Update an existing index.');
    }

    public function handle()
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', \dirname(__DIR__, 4));
        }
        $migrationDirectory = BASE_PATH . '/migrations/elasticsearch';

        if (!is_dir($migrationDirectory)) {
            $this->line('<fg=red>[ERROR]</> The migration directory does not exist.');
            return;
        }
        $indexName = $this->input->getArgument('index');
        $update = $this->input->getOption('update');
        $template = $update ? $this->updateTemplate($indexName) : $this->createTemplate($indexName);
        $migrationFile = sprintf('%s/%s-%s.php', $migrationDirectory, date('YmdHis'), $indexName);
        file_put_contents($migrationFile, $template);
        $this->line(sprintf('<fg=green>[OK]</> Migration file created at %s', $migrationFile));
        $this->line('     Run <fg=yellow>`php bin/hyperf.php elastic:migrate`</> to apply the migration.');
    }

    private function createTemplate(string $indexName): string
    {
        $template = file_get_contents(__DIR__ . '/stubs/migration-create.stub');
        return str_replace(['{{index}}'], [$indexName], $template);

    }

    private function updateTemplate(string $indexName): string
    {
        $template = file_get_contents(__DIR__ . '/stubs/migration-update.stub');
        return str_replace(['{{index}}'], [$indexName], $template);
    }
}
