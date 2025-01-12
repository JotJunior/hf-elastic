<?php

namespace Jot\HfElastic\Migration\ElasticTypes;

use Jot\HfElastic\Migration\AbstractField;

class TextType extends AbstractField
{

    public Type $type = Type::text;

    protected array $options = [
        'analyzer' => null,
        'eager_global_ordinals' => null,
        'fielddata' => null,
        'fielddata_requency_filter' => null,
        'fields' => null,
        'index' => null,
        'index_options' => null,
        'index_prefixes' => null,
        'index_phrases' => null,
        'norms' => null,
        'position_increment_gap' => null,
        'store' => null,
        'search_analyzer' => null,
        'search_quote_analyzer' => null,
        'similarity' => null,
        'term_vector' => null,
        'meta' => null,
    ];


    public function analyzer(string $value): self
    {
        $this->options['analyzer'] = $value;
        return $this;
    }

    public function eagerGlobalOrdinals(bool $value): self
    {
        $this->options['eager_global_ordinals'] = $value;
        return $this;
    }

    public function fielddata(bool $value): self
    {
        $this->options['fielddata'] = $value;
        return $this;
    }

    public function fielddataRequencyFilter(array $value): self
    {
        $this->options['fielddata_requency_filter'] = $value;
        return $this;
    }

    public function fields(array $value): self
    {
        $this->options['fields'] = $value;
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

    public function indexPrefixes(array $value): self
    {
        $this->options['index_prefixes'] = $value;
        return $this;
    }

    public function indexPhrases(bool $value): self
    {
        $this->options['index_phrases'] = $value;
        return $this;
    }

    public function norms(bool $value): self
    {
        $this->options['norms'] = $value;
        return $this;
    }

    public function positionIncrementGap(int $value): self
    {
        $this->options['position_increment_gap'] = $value;
        return $this;
    }

    public function store(bool $value): self
    {
        $this->options['store'] = $value;
        return $this;
    }

    public function searchAnalyzer(string $value): self
    {
        $this->options['search_analyzer'] = $value;
        return $this;
    }

    public function searchQuoteAnalyzer(string $value): self
    {
        $this->options['search_quote_analyzer'] = $value;
        return $this;
    }

    public function similarity(string $value): self
    {
        $this->options['similarity'] = $value;
        return $this;
    }

    public function termVector(string $value): self
    {
        $this->options['term_vector'] = $value;
        return $this;
    }

    public function meta(array $value): self
    {
        $this->options['meta'] = $value;
        return $this;
    }

}