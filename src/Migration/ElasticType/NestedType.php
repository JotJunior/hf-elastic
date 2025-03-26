<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Hyperf\Stringable\Str;
use Jot\HfElastic\Migration\Property;

class NestedType extends Property
{

    public Type $type = Type::nested;

    protected array $options = [
        'dynamic' => null,
        'properties' => null,
        'include_in_parent' => null,
        'include_in_root' => null,
    ];

    public function dynamic(bool $value): self
    {
        $this->options['dynamic'] = $value;
        return $this;
    }

    public function properties(array $properties): self
    {
        $this->options['properties'] = $properties;
        return $this;
    }

    public function includeInParent(bool $value): self
    {
        $this->options['include_in_parent'] = $value;
        return $this;
    }

    public function includeInRoot(bool $value): self
    {
        $this->options['include_in_root'] = $value;
        return $this;
    }

    /**
     * Retorna as propriedades do objeto aninhado
     * @return array
     */
    public function getProperties(): array
    {
        $properties = [];

        foreach ($this->fields as $field) {
            $options = $field->getOptions();
            $type = Str::snake($field->getType()->name);
            $properties[$field->getName()] = array_merge(['type' => $type], $options);
        }

        return $properties;
    }

}
