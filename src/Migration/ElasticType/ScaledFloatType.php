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

class ScaledFloatType extends Numeric
{
    public Type $type = Type::scaledFloat;

    protected array $options = [
        'scaling_factor' => null,
        'coerce' => null,
        'doc_values' => null,
        'ignore_malformed' => null,
        'index' => null,
        'null_value' => null,
        'on_script_error' => null,
        'script' => null,
        'store' => null,
    ];

    /**
     * Constructor.
     * @param string $name Field name
     * @param null|float $scalingFactor Optional scaling factor for the field
     */
    public function __construct(string $name, ?float $scalingFactor = null)
    {
        parent::__construct($name);

        if ($scalingFactor !== null) {
            $this->scalingFactor($scalingFactor);
        }
    }

    /**
     * Sets the scaling factor for the scaled_float type.
     * @param float $value The scaling factor
     */
    public function scalingFactor(float $value): self
    {
        $this->options['scaling_factor'] = $value;
        return $this;
    }
}
