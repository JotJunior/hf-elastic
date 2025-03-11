<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class KeywordType extends AbstractField
{

    public Type $type = Type::keyword;

    protected array $options = [
        'doc_values' => null,
        'eager_global_ordinals' => null,
        'fields' => null,
        'ignore_above' => null,
        'index' => null,
        'index_options' => null,
        'meta' => null,
        'norms' => true,
        'null_value' => null,
        'on_script_error' => null,
        'script' => null,
        'store' => null,
        'similarity' => null,
        'normalizer' => null,
        'split_queries_on_whitespace' => null,
        'time_series_dimension' => null,
    ];

    public function docValues(bool $value): self
    {
        $this->options['doc_values'] = $value;
        return $this;
    }

    public function eagerGlobalOrdinals(bool $value): self
    {
        $this->options['eager_global_ordinals'] = $value;
        return $this;
    }

    public function fields(array $value): self
    {
        $this->options['fields'] = $value;
        return $this;
    }

    public function ignoreAbove(int $value): self
    {
        $this->options['ignore_above'] = $value;
        return $this;
    }

    public function index(bool $value): self
    {
        $this->options['index'] = $value;
        return $this;
    }

    public function indexOptions(string $value): self
    {
        $this->options['index_options'] = $value;
        return $this;
    }

    public function meta(array $value): self
    {
        $this->options['meta'] = $value;
        return $this;
    }

    public function norms(bool $value = false): self
    {
        $this->options['norms'] = $value;
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

    public function similarity(string $value): self
    {
        $this->options['similarity'] = $value;
        return $this;
    }

    public function normalizer(string $value): self
    {
        $this->options['normalizer'] = $value;
        return $this;
    }

    public function splitQueriesOnWhitespace(bool $value): self
    {
        $this->options['split_queries_on_whitespace'] = $value;
        return $this;
    }

    public function timeSeriesDimension(bool $value): self
    {
        $this->options['time_series_dimension'] = $value;
        return $this;
    }


}