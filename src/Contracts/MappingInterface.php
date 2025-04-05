<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Contracts;

use Jot\HfElastic\Migration\ElasticType\Type;
use Jot\HfElastic\Migration\Property;

interface MappingInterface
{
    /**
     * Sets the name property of the mapping.
     * @param string $name the name to set
     * @return self the instance of the object
     */
    public function setName(string $name): self;

    /**
     * Gets the name of the mapping.
     * @return string the name of the mapping
     */
    public function getName(): string;

    /**
     * Configures the settings for the current instance.
     * @param array $settings an associative array of settings to be applied
     * @return self returns the instance of the current class
     */
    public function settings(array $settings): self;

    /**
     * Adds a property definition to the properties array with the specified field, type, and options.
     * @param string $field the name of the field to define
     * @param Type $type the type of the field
     * @param array $options additional options to merge with the property definition
     * @return self returns the instance of the current class
     */
    public function property(string $field, Type $type, array $options = []): self;

    /**
     * Generates the complete body for creating an index.
     * @return array the complete body structure for creating an index
     */
    public function body(): array;

    /**
     * Generates the body structure for updating an index mapping.
     * @return array the body structure for updating an index mapping
     */
    public function updateBody(): array;

    /**
     * Generates the mapping structure.
     * @param array $fields optional array of fields to use instead of the internal fields
     * @return array the generated mapping structure
     */
    public function generateMapping(array $fields = []): array;
}
