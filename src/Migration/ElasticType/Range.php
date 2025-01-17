<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class Range extends AbstractField
{

    protected array $options = [
        'coerce' => null,
        'doc_values' => null,
        'index' => null,
        'store' => null,
    ];

    public function coerce(bool $value): self
    {
        $this->options['coerce'] = $value;
        return $this;
    }

    public function docValues(bool $value): self
    {
        $this->options['doc_values'] = $value;
        return $this;
    }


    public function index(bool $value): self
    {
        $this->options['index'] = $value;
        return $this;
    }

    public function store(bool $value): self
    {
        $this->options['store'] = $value;
        return $this;
    }


}