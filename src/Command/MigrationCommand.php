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
use InvalidArgumentException;
use Jot\HfElastic\Services\FileGenerator;
use Jot\HfElastic\Services\TemplateGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

use function Hyperf\Translation\__;

#[Command]
class MigrationCommand extends AbstractCommand
{
    protected TemplateGenerator $templateGenerator;

    protected FileGenerator $fileGenerator;

    protected ?string $jsonSchema = null;

    protected ?string $json = null;

    protected bool $force = false;

    /**
     * MigrationCommand constructor.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        ContainerInterface $container,
        ?TemplateGenerator $templateGenerator = null,
        ?FileGenerator $fileGenerator = null
    ) {
        parent::__construct($container, 'elastic:migration');

        $this->templateGenerator = $templateGenerator ?? new TemplateGenerator(
            $this->container->get(ConfigInterface::class)
        );

        $this->fileGenerator = $fileGenerator ?? new FileGenerator();
    }

    /**
     * Configure the command.
     */
    public function configure(): void
    {
        $this->setDescription('Create a new migration for Elasticsearch.');
        $this->addUsage('elastic:migration --index=index_name');
        $this->addUsage('elastic:migration --index=index_name --update');
        $this->addArgument('index', InputOption::VALUE_REQUIRED, 'The index name.');
        $this->addOption('json-schema', 'S', InputOption::VALUE_OPTIONAL, 'JSON schema file path or url');
        $this->addOption('json', 'J', InputOption::VALUE_OPTIONAL, 'JSON file path or url');
        $this->addOption('update', 'U', InputOption::VALUE_NONE, 'Update an existing index.');
    }

    /**
     * Handle the command execution.
     * @return int
     */
    public function handle()
    {
        if (! $this->createMigrationDirectoryIfNotExists()) {
            $this->line(__('hf-elastic.console_migration_directory_failed'));
            return 1;
        }

        $indexName = $this->input->getArgument('index');
        $this->jsonSchema = $this->input->getOption('json-schema');
        $this->json = $this->input->getOption('json');
        $update = $this->input->getOption('update');

        try {
            if (! empty($this->jsonSchema) && ! empty($this->json)) {
                throw new InvalidArgumentException(__('hf-elastic.console_invalid_option'));
            }

            $template = $update
                ? $this->templateGenerator->generateUpdateTemplate($indexName)
                : $this->templateGenerator->generateCreateTemplate($indexName, $this->jsonSchema, $this->json);

            $migrationFile = $this->generateMigrationFilename($indexName, $update);

            $this->fileGenerator->generateFile($migrationFile, $template, $this, $this->force);
            $this->line(__('hf-elastic.console_migrate_command'));

            return 0;
        } catch (Throwable $e) {
            $this->line(sprintf('<fg=red>[ERROR]</> %s', $e->getMessage()));
            return 1;
        }
    }

    /**
     * Generate the migration filename based on the index name and operation type.
     * @param string $indexName the name of the index
     * @param bool $update whether this is an update migration
     * @return string the generated migration filename
     */
    protected function generateMigrationFilename(string $indexName, bool $update): string
    {
        return sprintf(
            '%s/%s-%s-%s.php',
            $this->migrationDirectory,
            date('YmdHis'),
            $update ? 'update' : 'create',
            $indexName
        );
    }
}
