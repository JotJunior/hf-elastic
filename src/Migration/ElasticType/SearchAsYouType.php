<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class SearchAsYouType extends KeywordType
{

    public Type $type = Type::searchAsYouType;

    protected array $options = [
        'max_shingle_size' => null,
        'analyzer' => null,
        'index' => null,
        'index_options' => null,
        'norms' => null,
        'store' => null,
        'search_analyzer' => null,
        'search_quote_analyzer' => null,
        'similarity' => null,
        'term_vector' => null,
    ];

    public function maxShingleSize(int $value): self
    {
        $this->options['max_shingle_size'] = $value;
        return $this;
    }

    public function analyzer(string $value): self
    {
        $this->options['analyzer'] = $value;
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

    public function norms(bool $value): self
    {
        $this->options['norms'] = $value;
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


}