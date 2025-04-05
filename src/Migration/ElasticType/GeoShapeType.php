<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Migration\ElasticType;

class GeoShapeType extends GeoPointType
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
