<?php

namespace Jot\HfElastic\Migration\ElasticType;

class GeoShape extends GeoPoint
{

    public Type $type = Type::geoShape;

    protected array $options = [
        'orientation' => null,
        'ignore_malformed' => null,
        'ignore_z_value' => null,
        'coerce' => null,
        'index' => null,
        'null_value' => null,
        'on_script_error' => null,
        'script' => null,
    ];

    public function orientation(string $value): self
    {
        $this->options['orientation'] = $value;
        return $this;
    }

    public function coerce(bool $value): self
    {
        $this->options['coerce'] = $value;
        return $this;
    }

}