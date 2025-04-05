<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Migration;

use Hyperf\Stringable\Str;
use Jot\HfElastic\Contracts\MappingInterface;
use Jot\HfElastic\Migration\ElasticType\Type;
use JsonSerializable;

class Mapping extends Property implements MappingInterface, JsonSerializable
{
    protected ?array $settings = null;

    protected array $fields = [];

    public function __construct(protected string $name, protected string $dynamic = 'strict')
    {
        parent::__construct($name);
    }

    /**
     * Sets the name property of the object.
     * @param string $name the name to set
     * @return self the instance of the object
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Configures the settings for the current instance.
     * @param array $settings an associative array of settings to be applied
     * @return self returns the instance of the current class
     */
    public function settings(array $settings): self
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * Adds a property definition to the properties array with the specified field, type, and options.
     * @param string $field the name of the field to define
     * @param Type $type the type of the field
     * @param array $options additional options to merge with the property definition
     * @return self returns the instance of the current class
     */
    public function property(string $field, Type $type, array $options = []): self
    {
        // Create a field object based on the type
        $typeName = $type->name;
        $className = '\Jot\HfElastic\Migration\ElasticType\\' . ucfirst(Str::camel($typeName)) . 'Type';
        if (class_exists($className)) {
            $fieldObject = new $className($field);
            if (! empty($options)) {
                $fieldObject->options($options);
            }
            $this->fields[] = $fieldObject;
        } else {
            // Fallback to the old way if class doesn't exist
            $this->fields[$field] = array_merge(['type' => Str::snake($typeName)], $options);
        }
        return $this;
    }

    /**
     * Generates the body structure for updating an index mapping.
     * @return array the body structure for updating an index mapping
     */
    public function updateBody(): array
    {
        return [
            'index' => $this->name,
            'body' => [
                ...$this->generateMapping(),
            ],
        ];
    }

    /**
     * Generates the mapping structure for the current index.
     * @param array $fields optional array of fields to use instead of the internal fields
     * @return array the generated mapping structure
     */
    public function generateMapping(array $fields = []): array
    {
        $mapping['properties'] = [];
        $fields = $fields ?: $this->fields;

        foreach ($fields as $key => $field) {
            // Check if $field is an object implementing FieldInterface or an associative array
            if (is_object($field) && method_exists($field, 'getType')) {
                // Handle object fields
                switch ($field->getType()) {
                    case Type::nested:
                        $mapping['properties'][$field->getName()] = [
                            'type' => 'nested',
                            ...$this->generateMapping($field->getChildren()),
                            ...$field->getOptions(),
                        ];
                        break;
                    case Type::object:
                    case Type::array_object:
                        $mapping['properties'][$field->getName()] = [
                            ...$this->generateMapping($field->getChildren()),
                            ...$field->getOptions(),
                        ];
                        break;
                    default:
                        $type = $field->getType();
                        $mapping['properties'][$field->getName()] = array_merge(['type' => Str::snake($type->name)], $field->getOptions());
                        break;
                }
            } else {
                // Handle associative array fields (legacy format)
                $mapping['properties'][$key] = $field;
            }
        }

        return $mapping;
    }

    public function generateFields(array $fields = []): array
    {
        $mapping['properties'] = [];
        $fields = $fields ?: $this->fields;

        foreach ($fields as $key => $field) {
            // Check if $field is an object implementing FieldInterface or an associative array
            if (is_object($field) && method_exists($field, 'getType')) {
                // Handle object fields
                switch ($field->getType()) {
                    case Type::nested:
                        $mapping['properties'][$field->getName()] = [
                            'type' => 'nested',
                            ...$this->generateMapping($field->getChildren()),
                            ...$field->getOptions(),
                        ];
                        break;
                    case Type::object:
                        $mapping['properties'][$field->getName()] = [
                            'type' => 'array_object',
                            ...$this->generateMapping($field->getChildren()),
                            ...$field->getOptions(),
                        ];
                        break;
                    case Type::array_object:
                        $mapping['properties'][$field->getName()] = [
                            'type' => 'object',
                            ...$this->generateMapping($field->getChildren()),
                            ...$field->getOptions(),
                        ];
                        break;
                    default:
                        $type = $field->getType();
                        $mapping['properties'][$field->getName()] = array_merge(['type' => Str::snake($type->name)], $field->getOptions());
                        break;
                }
            } else {
                // Handle associative array fields (legacy format)
                $mapping['properties'][$key] = $field;
            }
        }

        return $mapping;
    }

    /**
     * Convert the object into a format suitable for JSON serialization.
     * @return array an associative array representation of the object
     */
    public function jsonSerialize(): array
    {
        return [
            $this->name => $this->body(),
        ];
    }

    /**
     * Generates the complete body for creating an index.
     * @return array the complete body structure for creating an index
     */
    public function body(): array
    {
        return [
            'index' => $this->name,
            'body' => [
                'settings' => $this->settings,
                'mappings' => [
                    'dynamic' => $this->dynamic,
                    ...$this->generateMapping(),
                ],
            ],
        ];
    }

    /**
     * Cria um campo do tipo aggregate_metric_double.
     * @param string $name Nome do campo
     * @param array $metrics Lista de métricas
     */
    public function aggregateMetricDouble(string $name, array $metrics): self
    {
        $field = new ElasticType\AggregateMetricDoubleType($name);
        $field->metrics($metrics);
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Define a métrica padrão para o último campo aggregate_metric_double adicionado.
     * @param string $metric Métrica padrão
     */
    public function defaultMetric(string $metric): self
    {
        $lastField = end($this->fields);
        if ($lastField instanceof ElasticType\AggregateMetricDoubleType) {
            $lastField->defaultMetric($metric);
        }
        return $this;
    }

    /**
     * Cria um campo do tipo search_as_you_type.
     * @param string $name Nome do campo
     */
    public function searchAsYouType(string $name): self
    {
        $field = new ElasticType\SearchAsYouType($name);
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Define o analisador para o último campo adicionado.
     * @param string $analyzer Analisador
     */
    public function analyzer(string $analyzer): self
    {
        $lastField = end($this->fields);
        if (method_exists($lastField, 'analyzer')) {
            $lastField->analyzer($analyzer);
        }
        return $this;
    }

    /**
     * Define o tamanho máximo de shingle para o último campo search_as_you_type adicionado.
     * @param int $size Tamanho máximo de shingle
     */
    public function maxShingleSize(int $size): self
    {
        $lastField = end($this->fields);
        if ($lastField instanceof ElasticType\SearchAsYouType) {
            $lastField->maxShingleSize($size);
        }
        return $this;
    }
}
