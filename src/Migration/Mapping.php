<?php

namespace Jot\HfElastic\Migration;

use Hyperf\Stringable\Str;
use Jot\HfElastic\Contracts\MappingInterface;
use Jot\HfElastic\Migration\ElasticType\Type;

class Mapping extends Property implements MappingInterface
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
        // Create a field object based on the type
        $typeName = $type->name;
        $className = '\\Jot\\HfElastic\\Migration\\ElasticType\\' . ucfirst(Str::camel($typeName)) . 'Type';
        if (class_exists($className)) {
            $fieldObject = new $className($field);
            if (!empty($options)) {
                $fieldObject->options($options);
            }
            $this->fields[] = $fieldObject;
        } else {
            // Fallback to the old way if class doesn't exist
            $this->fields[$field] = array_merge(['type' => Str::snake($typeName)], $options);
        }
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
        
        foreach ($fields as $key => $field) {
            // Check if $field is an object implementing FieldInterface or an associative array
            if (is_object($field) && method_exists($field, 'getType')) {
                // Handle object fields
                switch ($field->getType()) {
                    case Type::nested:
                        $mapping['properties'][$field->getName()] = [
                            'type' => 'nested',
                            ...$this->generateMapping($field->getChildren()),
                            ...$field->getOptions()
                        ];
                        break;
                    case Type::object:
                        $mapping['properties'][$field->getName()] = [
                            ...$this->generateMapping($field->getChildren()),
                            ...$field->getOptions()
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
}