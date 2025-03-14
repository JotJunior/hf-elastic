<?php

namespace Jot\HfElastic\Migration;

use Jot\HfElastic\Migration\ElasticType\Type;

interface FieldInterface
{
    /**
     * Sets options for the field.
     * @param array $options The options to set.
     * @return self Returns the instance of the current class.
     */
    public function options(array $options): self;
    
    /**
     * Gets the name of the field.
     * @return string The name of the field.
     */
    public function getName(): string;
    
    /**
     * Gets the options of the field.
     * @return array The options of the field.
     */
    public function getOptions(): array;
    
    /**
     * Gets the type of the field.
     * @return Type The type of the field.
     */
    public function getType(): Type;
}
