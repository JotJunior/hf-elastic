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

use Jot\HfElastic\Migration\AbstractField;

class GeoPointType extends AbstractField
{
    public Type $type = Type::geoPoint;

    protected array $options = [
        'ignore_malformed' => null,
        'ignore_z_value' => null,
        'index' => null,
        'null_value' => null,
        'on_script_error' => null,
        'script' => null,
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

    public function index(bool $value): self
    {
        $this->options['index'] = $value;
        return $this;
    }

    public function nullValue(string $value): self
    {
        $this->options['null_value'] = $value;
        return $this;
    }

    public function onScriptError(string $value): self
    {
        $this->options['on_script_error'] = $value;
        return $this;
    }

    public function script(string $value): self
    {
        $this->options['script'] = $value;
        return $this;
    }
}
