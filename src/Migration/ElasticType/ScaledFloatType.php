<?php

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
     * Constructor
     * @param string $name Field name
     * @param float|null $scalingFactor Optional scaling factor for the field
     */
    public function __construct(string $name, ?float $scalingFactor = null)
    {
        parent::__construct($name);
        
        if ($scalingFactor !== null) {
            $this->scalingFactor($scalingFactor);
        }
    }
    
    /**
     * Sets the scaling factor for the scaled_float type
     * @param int $value The scaling factor
     * @return self
     */
    public function scalingFactor(int $value): self
    {
        $this->options['scaling_factor'] = $value;
        return $this;
    }
}
