<?php

namespace Jot\HfElastic\Contracts;

use Jot\HfElastic\Migration\ElasticType\Type;
use Jot\HfElastic\Migration\ElasticType\ObjectType;
use Jot\HfElastic\Migration\ElasticType\NestedType;

interface PropertyInterface
{
    /**
     * Gets the name of the property.
     * @return string The name of the property.
     */
    public function getName(): string;
    
    /**
     * Gets the type of the property.
     * @return Type The type of the property.
     */
    public function getType(): Type;
    
    /**
     * Gets the options of the property.
     * @return array The options of the property.
     */
    public function getOptions(): array;
    
    /**
     * Gets the children of the property.
     * @return array The children of the property.
     */
    public function getChildren(): array;
    
    /**
     * Adds an object type to the property.
     * @param ObjectType $object The object type to add.
     * @return ObjectType The added object type.
     */
    public function object(ObjectType $object): ObjectType;
    
    /**
     * Adds a nested type to the property.
     * @param NestedType $nested The nested type to add.
     * @return NestedType The added nested type.
     */
    public function nested(NestedType $nested): NestedType;
    
    /**
     * Adds default fields to the property.
     * @return void
     */
    public function defaults(): void;
}
