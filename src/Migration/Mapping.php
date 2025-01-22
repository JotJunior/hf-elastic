<?php

namespace Jot\HfElastic\Migration;

use Hyperf\Stringable\Str;
use Jot\HfElastic\Migration\ElasticType\Type;

class Mapping extends Property
{
    protected ?array $settings = null;
    protected array $fields = [];

    public function __construct(protected string $name, protected string $dynamic = 'strict')
    {
        parent::__construct($name);
    }

    /**
     * Sets the name property of the object.
     *
     * @param string $name The name to set.
     * @return self The instance of the object.
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

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
        $this->fields[$field] = array_merge(['type' => Str::snake($type->name)], $options);
        return $this;
    }

    public function body(): array
    {
        return [
            'index' => $this->name,
            'body' => [
                'settings' => $this->settings,
                'mappings' => [
                    'dynamic' => $this->dynamic,
                    ...$this->generateMapping()
                ],
            ],
        ];
    }

    public function updateBody(): array
    {
        return [
            'index' => $this->name,
            'body' => [
                ...$this->generateMapping()
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
                    $mapping['properties'][$field->getName()] = [
                        'type' => 'nested',
                        ...$this->generateMapping($field->getChildren()),
                        ...$this->getOptions()
                    ];
                    break;
                case Type::object:
                    $mapping['properties'][$field->getName()] = [
                        ...$this->generateMapping($field->getChildren()),
                        ...$this->getOptions()
                    ];
                    break;
                default:
                    $mapping['properties'][$field->getName()] = array_merge(['type' => Str::snake($field->getType()->name)], $field->getOptions());
                    break;
            }
        }
        return $mapping;
    }


}