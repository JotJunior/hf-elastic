<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class Point extends AbstractField
{

    public Type $type = Type::geoPoint;

    protected array $options = [
        'ignore_malformed' => null,
        'ignore_z_value' => null,
        'null_value' => null,
    ];

    public function ignoreMalformed(bool $value): self
    {
        $this->options['ignore_malformed'] = $value;
        return $this;
    }

    public function ignoreZValue(bool $value): self
    {
        $this->options['ignore_z_value'] = $value;
        return $this;
    }


    public function nullValue(string $value): self
    {
        $this->options['null_value'] = $value;
        return $this;
    }


}