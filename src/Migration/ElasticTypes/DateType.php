<?php

namespace Jot\HfElastic\Migration\ElasticTypes;

use Jot\HfElastic\Migration\AbstractField;

class DateType extends AbstractField
{

    public Type $type = Type::date;

    protected array $options = [
        'doc_values' => null,
        'format' => null,
        'locale' => null,
        'ignore_malformed' => null,
        'index' => null,
        'null_value' => null,
        'on_script_error' => null,
        'script' => null,
        'store' => null,
        'meta' => null,
    ];

    public function docValues(bool $value): self
    {
        $this->options['doc_values'] = $value;
        return $this;
    }

    public function format(bool $value): self
    {
        $this->options['format'] = $value;
        return $this;
    }

    public function locale(bool $value): self
    {
        $this->options['locale'] = $value;
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

    public function nullValue(bool $value): self
    {
        $this->options['null_value'] = $value;
        return $this;
    }

    public function on_script_error(bool $value): self
    {
        $this->options['on_script_error'] = $value;
        return $this;
    }

    public function script(bool $value): self
    {
        $this->options['script'] = $value;
        return $this;
    }

    public function store(bool $value): self
    {
        $this->options['store'] = $value;
        return $this;
    }

    public function meta(bool $value): self
    {
        $this->options['meta'] = $value;
        return $this;
    }

}