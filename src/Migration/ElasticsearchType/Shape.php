<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

class Shape extends GeoPoint
{

    public Type $type = Type::shape;

    protected array $options = [
        'orientation' => null,
        'ignore_malformed' => null,
        'ignore_z_value' => null,
        'coerce' => null,
    ];

    public function orientation(string $value): self
    {
        $this->options['orientation'] = $value;
        return $this;
    }

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

    public function coerce(bool $value): self
    {
        $this->options['coerce'] = $value;
        return $this;
    }

}