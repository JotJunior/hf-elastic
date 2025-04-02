<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\Services\FileGenerator;
use Jot\HfElastic\Services\TemplateGenerator;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;
use function Hyperf\Translation\__;

#[Command]
class MigrationCommand extends AbstractCommand
{
    /**
     * @var TemplateGenerator
     */
    protected TemplateGenerator $templateGenerator;

    /**
     * @var FileGenerator
     */
    protected FileGenerator $fileGenerator;

    /**
     * @var ?string
     */
    protected ?string $jsonSchema = null;

    /**
     * @var ?string
     */
    protected ?string $json = null;

    /**
     * @var bool
     */
    protected bool $force = false;

    /**
     * MigrationCommand constructor.
     * @param ContainerInterface $container
     * @param TemplateGenerator|null $templateGenerator
     * @param FileGenerator|null $fileGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(
        ContainerInterface $container,
        ?TemplateGenerator $templateGenerator = null,
        ?FileGenerator     $fileGenerator = null
    )
    {
        parent::__construct($container, 'elastic:migration');

        $this->templateGenerator = $templateGenerator ?? new TemplateGenerator(
            $this->container->get(ConfigInterface::class)
        );

        $this->fileGenerator = $fileGenerator ?? new FileGenerator();
    }

    /**
     * Configure the command.
     * @return void
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
        if (!$this->createMigrationDirectoryIfNotExists()) {
            $this->line(__('hf-elastic.console_migration_directory_failed'));
            return 1;
        }

        $indexName = $this->input->getArgument('index');
        $this->jsonSchema = $this->input->getOption('json-schema');
        $this->json = $this->input->getOption('json');
        $update = $this->input->getOption('update');

        try {
            if (!empty($this->jsonSchema) && !empty($this->json)) {
                throw new \InvalidArgumentException(__('hf-elastic.console_invalid_option'));
            }

            $template = $update
                ? $this->templateGenerator->generateUpdateTemplate($indexName)
                : $this->templateGenerator->generateCreateTemplate($indexName, $this->jsonSchema, $this->json);

            $migrationFile = $this->generateMigrationFilename($indexName, $update);

            $this->fileGenerator->generateFile($migrationFile, $template, $this, $this->force);
            $this->line(__('hf-elastic.console_migrate_command'));

            return 0;
        } catch (\Throwable $e) {
            $this->line(sprintf('<fg=red>[ERROR]</> %s', $e->getMessage()));
            return 1;
        }
    }


    /**
     * Generate the migration filename based on the index name and operation type.
     * @param string $indexName The name of the index.
     * @param bool $update Whether this is an update migration.
     * @return string The generated migration filename.
     */
    protected function generateMigrationFilename(string $indexName, bool $update): string
    {
        return sprintf('%s/%s-%s-%s.php',
            $this->migrationDirectory,
            date('YmdHis'),
            $update ? 'update' : 'create',
            $indexName
        );
    }

}
