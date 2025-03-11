<?php

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
     * Define o analisador para o campo
     * 
     * @param string $analyzer
     * @return self
     */
    public function analyzer(string $analyzer): self
    {
        $this->options['analyzer'] = $analyzer;
        return $this;
    }
    
    /**
     * Define o analisador de busca para o campo
     * 
     * @param string $analyzer
     * @return self
     */
    public function searchAnalyzer(string $analyzer): self
    {
        $this->options['search_analyzer'] = $analyzer;
        return $this;
    }
    
    /**
     * Define o analisador de busca para aspas
     * 
     * @param string $analyzer
     * @return self
     */
    public function searchQuoteAnalyzer(string $analyzer): self
    {
        $this->options['search_quote_analyzer'] = $analyzer;
        return $this;
    }
    
    /**
     * Define o tamanho máximo de shingle
     * 
     * @param int $size
     * @return self
     */
    public function maxShingleSize(int $size): self
    {
        $this->options['max_shingle_size'] = $size;
        return $this;
    }
    
    /**
     * Define se o campo deve ser indexado
     * 
     * @param bool $index
     * @return self
     */
    public function index(bool $index): self
    {
        $this->options['index'] = $index;
        return $this;
    }
    
    /**
     * Define se o campo deve usar normas
     * 
     * @param bool $norms
     * @return self
     */
    public function norms(bool $norms): self
    {
        $this->options['norms'] = $norms;
        return $this;
    }
    
    /**
     * Define se o campo deve ser armazenado
     * 
     * @param bool $store
     * @return self
     */
    public function store(bool $store): self
    {
        $this->options['store'] = $store;
        return $this;
    }
    
    /**
     * Define a similaridade para o campo
     * 
     * @param string $similarity
     * @return self
     */
    public function similarity(string $similarity): self
    {
        $this->options['similarity'] = $similarity;
        return $this;
    }
    
    /**
     * Define o vetor de termo para o campo
     * 
     * @param string $termVector
     * @return self
     */
    public function termVector(string $termVector): self
    {
        $this->options['term_vector'] = $termVector;
        return $this;
    }
    
    /**
     * Define o campo para o qual este campo deve ser copiado
     * 
     * @param string|array $copyTo
     * @return self
     */
    public function copyTo($copyTo): self
    {
        $this->options['copy_to'] = $copyTo;
        return $this;
    }
}
