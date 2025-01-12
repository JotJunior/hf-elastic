<?php

namespace Jot\HfElastic\Migration\ElasticTypes;

use Jot\HfElastic\Migration\Property;

class Nested extends Property
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

}