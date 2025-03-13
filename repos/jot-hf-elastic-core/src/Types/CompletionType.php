<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Types;

/**
 * Completion field type for Elasticsearch.
 * Used for auto-complete functionality.
 */
class CompletionType extends AbstractElasticType
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'completion');
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
     * Set the preserve_separators parameter.
     *
     * @param bool $preserveSeparators
     * @return self
     */
    public function setPreserveSeparators(bool $preserveSeparators): self
    {
        return $this->setProperty('preserve_separators', $preserveSeparators);
    }

    /**
     * Set the preserve_position_increments parameter.
     *
     * @param bool $preservePositionIncrements
     * @return self
     */
    public function setPreservePositionIncrements(bool $preservePositionIncrements): self
    {
        return $this->setProperty('preserve_position_increments', $preservePositionIncrements);
    }

    /**
     * Set the max_input_length parameter.
     *
     * @param int $maxInputLength
     * @return self
     */
    public function setMaxInputLength(int $maxInputLength): self
    {
        return $this->setProperty('max_input_length', $maxInputLength);
    }
}
