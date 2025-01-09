<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\Property;

class ObjectType extends Property
{

    public Type $type = Type::object;

    protected array $options = [
        'dynamic' => null,
        'enabled' => null,
        'subobjects' => null,
        'properties' => null,
    ];

    public function dynamic(bool $value): self
    {
        $this->options['dynamic'] = $value;
        return $this;
    }

    public function enabled(bool $value): self
    {
        $this->options['enabled'] = $value;
        return $this;
    }

    public function subobjects(bool $value): self
    {
        $this->options['subobjects'] = $value;
        return $this;
    }

}