<?php

namespace Jot\HfElastic\Contracts;

use Jot\HfElastic\Migration\ElasticType\Type;
use Jot\HfElastic\Migration\Property;

interface MappingInterface
{
    /**
     * Sets the name property of the mapping.
     * @param string $name The name to set.
     * @return self The instance of the object.
     */
    public function setName(string $name): self;

    /**
     * Gets the name of the mapping.
     * @return string The name of the mapping.
     */
    public function getName(): string;

    /**
     * Configures the settings for the current instance.
     * @param array $settings An associative array of settings to be applied.
     * @return self Returns the instance of the current class.
     */
    public function settings(array $settings): self;

    /**
     * Adds a property definition to the properties array with the specified field, type, and options.
     * @param string $field The name of the field to define.
     * @param Type $type The type of the field.
     * @param array $options Additional options to merge with the property definition.
     * @return self Returns the instance of the current class.
     */
    public function property(string $field, Type $type, array $options = []): self;

    /**
     * Generates the complete body for creating an index.
     * @return array The complete body structure for creating an index.
     */
    public function body(): array;

    /**
     * Generates the body structure for updating an index mapping.
     * @return array The body structure for updating an index mapping.
     */
    public function updateBody(): array;

    /**
     * Generates the mapping structure.
     * @param array $fields Optional array of fields to use instead of the internal fields.
     * @return array The generated mapping structure.
     */
    public function generateMapping(array $fields = []): array;
}
