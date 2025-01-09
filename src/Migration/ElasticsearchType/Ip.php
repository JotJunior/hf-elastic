<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class Ip extends AbstractField
{

    public Type $type = Type::ip;

    protected array $options = [
        'doc_values' => null,
        'ignore_malformed' => null,
        'index' => null,
        'null_value' => null,
        'on_script_error' => null,
        'script' => null,
        'store' => null,
        'time_series_dimension' => null,
    ];

    public function docValues(bool $value): self
    {
        $this->options['doc_values'] = $value;
        return $this;
    }

    public function ignoreMalformed(bool $value): self
    {
        $this->options['ignore_malformed'] = $value;
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

    public function store(bool $value): self
    {
        $this->options['store'] = $value;
        return $this;
    }

    public function timeSeriesDimension(bool $value): self
    {
        $this->options['time_series_dimension'] = $value;
        return $this;
    }

}