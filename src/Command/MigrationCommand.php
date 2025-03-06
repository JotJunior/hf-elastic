<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\Migration\Helper\Json;
use Jot\HfElastic\Migration\Helper\JsonSchema;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class MigrationCommand extends HyperfCommand
{

    protected ConfigInterface $config;

    protected ?string $jsonSchema = null;
    protected ?string $json = null;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('elastic:migration');
        $this->config = $this->container->get(ConfigInterface::class);
    }

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

    public function handle()
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', \dirname(__DIR__, 4));
        }
        $migrationDirectory = BASE_PATH . '/migrations/elasticsearch';

        if (!is_dir($migrationDirectory)) {
            mkdir($migrationDirectory, 0755, true);
        }

        $indexName = $this->input->getArgument('index');
        $this->jsonSchema = $this->input->getOption('json-schema');
        $this->json = $this->input->getOption('json');

        if (!empty($this->jsonSchema) && !empty($this->json)) {
            $this->line('<fg=red>[ERROR]</> You can only use one of the options --json-schema or --json');
            return;
        }

        $update = $this->input->getOption('update');
        try {
            $template = $update ? $this->updateTemplate($indexName) : $this->createTemplate($indexName);
        } catch (\Throwable $e) {
            $this->line(sprintf('<fg=red>[ERROR]</> %s', $e->getMessage()));
            return;
        }

        $migrationFile = sprintf('%s/%s-%s-%s.php',
            $migrationDirectory,
            date('YmdHis'), $update ? 'update' : 'create',
            $indexName
        );


        $this->generateFile($migrationFile, $template);
        $this->line('     Run <fg=yellow>`php bin/hyperf.php elastic:migrate`</> to apply the migration.');
    }


    /**
     * Updates a template by parsing it with the specified index name.
     *
     * @param string $indexName The name of the index to be applied in the template.
     * @return string The parsed template with the provided variables.
     */
    private function updateTemplate(string $indexName): string
    {
        $variables = [
            'index' => $indexName,
        ];
        return $this->parseTemplate('migration-update', $variables);
    }

    /**
     * Creates a template by replacing placeholders within a template file with provided variables.
     *
     * @param string $name The name of the template file (without extension) to be processed.
     * @param array $variables An associative array of placeholders and their replacement values.
     *
     * @return string The processed template with placeholders replaced by their corresponding values.
     */
    private function parseTemplate(string $name, array $variables): string
    {
        $template = file_get_contents(sprintf('%s/stubs/%s.stub', __DIR__, $name));
        array_walk($variables, function ($value, $key) use (&$template) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        });

        return $template;
    }

    /**
     * Creates a template string based on the given index name and optional file path.
     * If a file path is provided, it processes the file using a JSON schema and incorporates its content
     * into the template. Otherwise, it generates a default template.
     *
     * @param string $indexName The name of the index for which the template is being created.
     * @return string The generated template string based on the provided inputs.
     * @throws \Exception
     */
    private function createTemplate(string $indexName): string
    {
        $variables = [
            'index' => $indexName,
            'dynamic' => $this->getDynamic(),
            'settings' => $this->getSettings(),
            'contents' => '',
        ];

        $template = 'migration-create';
        $indentation = str_repeat(' ', 8);
        if (!empty($this->jsonSchema)) {
            $template = 'migration-json';
            $variables['contents'] = preg_replace('/^/m', $indentation, (new JsonSchema($this->jsonSchema))->body());
        } elseif (!empty($this->json)) {
            $template = 'migration-json';
            $variables['contents'] = preg_replace('/^/m', $indentation, (new Json($this->json))->body());
        }
        return $this->parseTemplate($template, $variables);

    }

    /**
     * Retrieves the dynamic configuration value from the application's configuration settings.
     * Returns a default value if the configuration key is not set.
     *
     * @return string The value of the dynamic configuration setting, or the default value 'strict' if not defined.
     */
    protected function getDynamic(): string
    {
        return $this->config->get('hf_elastic')['dynamic'] ?? 'strict';
    }

    /**
     * Retrieves and formats the settings for the Elasticsearch index.
     * The settings include configurations for shards, replicas, and analysis normalizers.
     * The resulting settings are formatted as a PHP string representation suitable for exporting.
     *
     * @return string The formatted settings string for the Elasticsearch index.
     */
    protected function getSettings(): string
    {
        $settings = $this->config->get('hf_elastic')['settings'] ?? [];

        if (empty($settings)) {
            $settings = [
                'index' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1,
                ],
                'analysis' => [
                    'normalizer' => [
                        'normalizer_ascii_lower' => [
                            'type' => 'custom',
                            'char_filter' => [],
                            'filter' => ['asciifolding', 'lowercase']
                        ]
                    ]
                ]
            ];
        }

        $export = str_replace(['array (', ')', "=> \n"], ['[', ']', "=> "], var_export($settings, true));
        $indentation = str_repeat(' ', 12);
        return preg_replace('/^/m', $indentation, $export);
    }

    /**
     * Generates a file with the specified contents and writes it to the given output location.
     * Prompts the user for confirmation if a file with the same name already exists.
     *
     * @param string $outputFile The path to the file to be generated.
     * @param string $contents The content to be written to the file.
     * @return void
     */
    protected function generateFile(string $outputFile, string $contents): void
    {
        if (file_exists($outputFile) && !$this->force) {
            $answer = $this->ask(sprintf('The file <fg=yellow>%s</> already exists. Overwrite file? [y/n/a]', $outputFile), 'n');
            if ($answer === 'a') {
                $this->force = true;
            } elseif ($answer !== 'y') {
                $this->line(sprintf('<fg=yellow>[SKIP]</> %s', $outputFile));
                return;
            }
        }

        file_put_contents($outputFile, $contents);
        $this->line(sprintf('<fg=green>[OK]</> %s', $outputFile));

    }

}
