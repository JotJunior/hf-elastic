<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class HistogramType extends AbstractField
{

    public Type $type = Type::histogram;
    
    protected array $options = [
        'ignore_malformed' => null,
        'store' => null,
        'doc_values' => null,
    ];
    
    /**
     * Define se valores malformados devem ser ignorados
     * 
     * @param bool $value
     * @return self
     */
    public function ignoreMalformed(bool $value): self
    {
        $this->options['ignore_malformed'] = $value;
        return $this;
    }
    
    /**
     * Define se o campo deve ser armazenado
     * 
     * @param bool $value
     * @return self
     */
    public function store(bool $value): self
    {
        $this->options['store'] = $value;
        return $this;
    }
    
    /**
     * Define se o campo deve ser armazenado em doc_values
     * 
     * @param bool $value
     * @return self
     */
    public function docValues(bool $value): self
    {
        $this->options['doc_values'] = $value;
        return $this;
    }
}