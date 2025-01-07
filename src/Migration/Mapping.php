<?php

namespace Jot\HfElastic\Migration;

use Jot\HfElastic\Migration\ElasticsearchType\Type;

class Mapping extends Property
{
    protected ?array $settings = null;
    protected array $fields = [];

    /**
     * Configures the settings for the current instance.
     *
     * @param array $settings An associative array of settings to be applied.
     * @return self Returns the instance of the current class.
     */
    public function settings(array $settings): self
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * Adds a property definition to the properties array with the specified field, type, and options.
     *
     * @param string $field The name of the field to define.
     * @param Type $type The type of the field.
     * @param array $options Additional options to merge with the property definition.
     * @return self Returns the instance of the current class.
     */
    public function property(string $field, Type $type, array $options = []): self
    {
        $this->fields[$field] = array_merge(['type' => $type->name], $options);
        return $this;
    }

    public function body(): array
    {
        return [
            'index' => $this->name,
            'body' => [
                'settings' => $this->settings,
                'mappings' => [
                    'dynamic' => 'strict',
                    ...$this->generateMapping()
                ],
            ],
        ];
    }

    public function generateMapping(array $fields = []): array
    {
        $mapping['properties'] = [];
        $fields = $fields ?: $this->fields;
        foreach ($fields as $field) {
            switch ($field->getType()) {
                case Type::nested:
                    $mapping['properties'][$field->getName()] = array_merge(['type' => 'nested'], $this->generateMapping($field->getChildren()));
                    break;
                case Type::object:
                    $mapping['properties'][$field->getName()] = $this->generateMapping($field->getChildren());
                    break;
                default:
                    $mapping['properties'][$field->getName()] = array_merge(['type' => $field->getType()->name], $field->getOptions());
                    break;
            }
        }
        return $mapping;
    }


}