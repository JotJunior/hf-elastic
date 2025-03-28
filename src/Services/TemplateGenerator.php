<?php

declare(strict_types=1);

namespace Jot\HfElastic\Services;

use Hyperf\Contract\ConfigInterface;
use Jot\HfElastic\Exception\InvalidTemplateFormatException;
use Jot\HfElastic\Migration\Helper\Json;
use Jot\HfElastic\Migration\Helper\JsonSchema;
use function Hyperf\Translation\__;

class TemplateGenerator
{
    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * TemplateGenerator constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Generate a template for updating an index.
     * @param string $indexName The name of the index to update.
     * @return string The generated template.
     */
    public function generateUpdateTemplate(string $indexName): string
    {
        $variables = [
            'index' => $indexName,
        ];

        return $this->parseTemplate('migration-update', $variables);
    }

    /**
     * Parse a template with variables.
     * @param string $name The name of the template.
     * @param array $variables The variables to replace in the template.
     * @return string The parsed template.
     */
    protected function parseTemplate(string $name, array $variables): string
    {
        $template = file_get_contents(sprintf('%s/stubs/%s.stub', dirname(__DIR__) . '/Command', $name));

        array_walk($variables, function ($value, $key) use (&$template) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        });

        return $template;
    }

    /**
     * Generate a template for creating an index.
     * @param string $indexName The name of the index to create.
     * @param string|null $jsonSchemaPath Path to a JSON schema file.
     * @param string|null $jsonPath Path to a JSON file.
     * @return string The generated template.
     * @throws \Exception If both jsonSchemaPath and jsonPath are provided.
     */
    public function generateCreateTemplate(string $indexName, ?string $jsonSchemaPath = null, ?string $jsonPath = null): string
    {
        if (!empty($jsonSchemaPath) && !empty($jsonPath)) {
            throw new InvalidTemplateFormatException();
        }

        $variables = [
            'index' => $indexName,
            'dynamic' => $this->getDynamic(),
            'settings' => $this->getSettings(),
            'contents' => '',
        ];

        $template = 'migration-create';
        $indentation = str_repeat(' ', 8);

        if (!empty($jsonSchemaPath)) {
            $template = 'migration-json';
            $variables['contents'] = preg_replace('/^/m', $indentation, (new JsonSchema($jsonSchemaPath))->body());
        } elseif (!empty($jsonPath)) {
            $template = 'migration-json';
            $variables['contents'] = preg_replace('/^/m', $indentation, (new Json($jsonPath))->body());
        }

        return $this->parseTemplate($template, $variables);
    }

    /**
     * Get the dynamic setting from the configuration.
     * @return string The dynamic setting value.
     */
    protected function getDynamic(): string
    {
        return $this->config->get('hf_elastic')['dynamic'] ?? 'strict';
    }

    /**
     * Get the settings for the Elasticsearch index.
     * @return string The formatted settings.
     */
    protected function getSettings(): string
    {
        $settings = $this->config->get('hf_elastic')['settings'] ?? [];

        if (empty($settings)) {
            $settings = [
                'index' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
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
}
