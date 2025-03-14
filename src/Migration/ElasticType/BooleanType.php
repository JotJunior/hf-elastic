<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class BooleanType extends AbstractField
{

    public Type $type = Type::boolean;
    
    protected array $options = [
        'boost' => null,
        'doc_values' => null,
        'index' => null,
        'null_value' => null,
        'store' => null,
    ];
    
    /**
     * Define o boost para o campo
     * @param float $value
     * @return self
     */
    public function boost(float $value): self
    {
        $this->options['boost'] = $value;
        return $this;
    }
    
    /**
     * Define se o campo deve ser armazenado em doc_values
     * @param bool $value
     * @return self
     */
    public function docValues(bool $value): self
    {
        $this->options['doc_values'] = $value;
        return $this;
    }
    
    /**
     * Define se o campo deve ser indexado
     * @param bool $value
     * @return self
     */
    public function index(bool $value): self
    {
        $this->options['index'] = $value;
        return $this;
    }
    
    /**
     * Define o valor a ser usado quando o campo é nulo
     * @param bool $value
     * @return self
     */
    public function nullValue(bool $value): self
    {
        $this->options['null_value'] = $value;
        return $this;
    }
    
    /**
     * Define se o campo deve ser armazenado
     * @param bool $value
     * @return self
     */
    public function store(bool $value): self
    {
        $this->options['store'] = $value;
        return $this;
    }
}
