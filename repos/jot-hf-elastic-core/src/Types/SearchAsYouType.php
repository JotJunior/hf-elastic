<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Types;

/**
 * Search As You Type field type for Elasticsearch.
 * Used for providing auto-complete functionality.
 */
class SearchAsYouType extends AbstractElasticType
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'search_as_you_type');
        $this->searchable = true;
    }

    /**
     * Set analyzer for the field.
     *
     * @param string $analyzer
     * @return self
     */
    public function setAnalyzer(string $analyzer): self
    {
        return $this->setProperty('analyzer', $analyzer);
    }

    /**
     * Set search analyzer for the field.
     *
     * @param string $searchAnalyzer
     * @return self
     */
    public function setSearchAnalyzer(string $searchAnalyzer): self
    {
        return $this->setProperty('search_analyzer', $searchAnalyzer);
    }

    /**
     * Set index options for the field.
     *
     * @param string $indexOptions
     * @return self
     */
    public function setIndexOptions(string $indexOptions): self
    {
        return $this->setProperty('index_options', $indexOptions);
    }

    /**
     * Set max shingle size for the field.
     *
     * @param int $maxShingleSize
     * @return self
     */
    public function setMaxShingleSize(int $maxShingleSize): self
    {
        return $this->setProperty('max_shingle_size', $maxShingleSize);
    }

    /**
     * Set whether field values should be stored.
     *
     * @param bool $store
     * @return self
     */
    public function setStore(bool $store): self
    {
        return $this->setProperty('store', $store);
    }

    /**
     * Set similarity algorithm for the field.
     *
     * @param string $similarity
     * @return self
     */
    public function setSimilarity(string $similarity): self
    {
        return $this->setProperty('similarity', $similarity);
    }

    /**
     * Set term vector options for the field.
     *
     * @param string $termVector
     * @return self
     */
    public function setTermVector(string $termVector): self
    {
        return $this->setProperty('term_vector', $termVector);
    }

    /**
     * Set the boost parameter.
     *
     * @param float $boost
     * @return self
     */
    public function setBoost(float $boost): self
    {
        return $this->setProperty('boost', $boost);
    }
}
