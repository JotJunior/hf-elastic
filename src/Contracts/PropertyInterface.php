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

use Jot\HfElastic\Migration\ElasticType\NestedType;
use Jot\HfElastic\Migration\ElasticType\ObjectType;
use Jot\HfElastic\Migration\ElasticType\Type;

interface PropertyInterface
{
    /**
     * Gets the name of the property.
     * @return string the name of the property
     */
    public function getName(): string;

    /**
     * Gets the type of the property.
     * @return Type the type of the property
     */
    public function getType(): Type;

    /**
     * Gets the options of the property.
     * @return array the options of the property
     */
    public function getOptions(): array;

    /**
     * Gets the children of the property.
     * @return array the children of the property
     */
    public function getChildren(): array;

    /**
     * Adds an object type to the property.
     * @param ObjectType $object the object type to add
     * @return ObjectType the added object type
     */
    public function object(ObjectType $object): ObjectType;

    /**
     * Adds a nested type to the property.
     * @param NestedType $nested the nested type to add
     * @return NestedType the added nested type
     */
    public function nested(NestedType $nested): NestedType;

    /**
     * Adds default fields to the property.
     */
    public function defaults(): void;
}
