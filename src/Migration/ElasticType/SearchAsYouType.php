<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class SearchAsYouType extends AbstractField
{
    public Type $type = Type::searchAsYouType;

    protected array $options = [
        'analyzer' => null,
        'search_analyzer' => null,
        'search_quote_analyzer' => null,
        'max_shingle_size' => null,
        'index' => null,
        'norms' => null,
        'store' => null,
        'similarity' => null,
        'term_vector' => null,
        'copy_to' => null,
    ];

    /**
     * Define o analisador para o campo.
     */
    public function analyzer(string $analyzer): self
    {
        $this->options['analyzer'] = $analyzer;
        return $this;
    }

    /**
     * Define o analisador de busca para o campo.
     */
    public function searchAnalyzer(string $analyzer): self
    {
        $this->options['search_analyzer'] = $analyzer;
        return $this;
    }

    /**
     * Define o analisador de busca para aspas.
     */
    public function searchQuoteAnalyzer(string $analyzer): self
    {
        $this->options['search_quote_analyzer'] = $analyzer;
        return $this;
    }

    /**
     * Define o tamanho máximo de shingle.
     */
    public function maxShingleSize(int $size): self
    {
        $this->options['max_shingle_size'] = $size;
        return $this;
    }

    /**
     * Define se o campo deve ser indexado.
     */
    public function index(bool $index): self
    {
        $this->options['index'] = $index;
        return $this;
    }

    /**
     * Define se o campo deve usar normas.
     */
    public function norms(bool $norms): self
    {
        $this->options['norms'] = $norms;
        return $this;
    }

    /**
     * Define se o campo deve ser armazenado.
     */
    public function store(bool $store): self
    {
        $this->options['store'] = $store;
        return $this;
    }

    /**
     * Define a similaridade para o campo.
     */
    public function similarity(string $similarity): self
    {
        $this->options['similarity'] = $similarity;
        return $this;
    }

    /**
     * Define o vetor de termo para o campo.
     */
    public function termVector(string $termVector): self
    {
        $this->options['term_vector'] = $termVector;
        return $this;
    }

    /**
     * Define o campo para o qual este campo deve ser copiado.
     * @param array|string $copyTo
     */
    public function copyTo($copyTo): self
    {
        $this->options['copy_to'] = $copyTo;
        return $this;
    }
}
