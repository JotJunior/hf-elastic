<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Types;

/**
 * Text field type for Elasticsearch.
 * Used for full-text search on analyzed text fields.
 */
class TextType extends AbstractElasticType
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'text');
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
     * Add a field to this field.
     *
     * @param string $name
     * @param array $properties
     * @return self
     */
    public function addField(string $name, array $properties): self
    {
        $fields = $this->getProperty('fields', []);
        $fields[$name] = $properties;

        return $this->setProperty('fields', $fields);
    }

    /**
     * Add a keyword field to this text field.
     *
     * @param string $name
     * @return self
     */
    public function addKeywordField(string $name = 'keyword'): self
    {
        return $this->addField($name, ['type' => 'keyword']);
    }
}
