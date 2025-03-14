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
     * Define o fator de escala para o tipo scaled_float
     * @param int $value O fator de escala
     * @return self
     */
    public function scalingFactor(int $value): self
    {
        $this->options['scaling_factor'] = $value;
        return $this;
    }
}
