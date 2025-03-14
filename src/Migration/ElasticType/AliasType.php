<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class AliasType extends AbstractField
{

    public Type $type = Type::alias;

    protected array $options = [
        'path' => null,
    ];

    public function path(string $path): self
    {
        $this->options['path'] = $path;
        return $this;
    }

}
