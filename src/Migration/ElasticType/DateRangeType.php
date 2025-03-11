<?php

namespace Jot\HfElastic\Migration\ElasticType;

class DateRangeType extends RangeType
{

    public Type $type = Type::dateRange;
    
    protected array $options = [
        'coerce' => null,
        'doc_values' => null,
        'format' => null,
        'index' => null,
        'store' => null,
    ];
    
    /**
     * Define o formato de data para o tipo date_range
     * 
     * @param string $value O formato de data (ex: 'yyyy-MM-dd')
     * @return self
     */
    public function format(string $value): self
    {
        $this->options['format'] = $value;
        return $this;
    }
}