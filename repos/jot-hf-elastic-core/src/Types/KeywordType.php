<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Types;

/**
 * Keyword field type for Elasticsearch.
 * Used for exact matching, sorting, and aggregations.
 */
class KeywordType extends AbstractElasticType
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'keyword');
        $this->filterable = true;
        $this->sortable = true;
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
     * Set the doc_values parameter.
     *
     * @param bool $docValues
     * @return self
     */
    public function setDocValues(bool $docValues): self
    {
        return $this->setProperty('doc_values', $docValues);
    }

    /**
     * Set the ignore_above parameter.
     *
     * @param int $ignoreAbove
     * @return self
     */
    public function setIgnoreAbove(int $ignoreAbove): self
    {
        return $this->setProperty('ignore_above', $ignoreAbove);
    }

    /**
     * Set the normalizer parameter.
     *
     * @param string $normalizer
     * @return self
     */
    public function setNormalizer(string $normalizer): self
    {
        return $this->setProperty('normalizer', $normalizer);
    }

    /**
     * Set the null_value parameter.
     *
     * @param string $nullValue
     * @return self
     */
    public function setNullValue(string $nullValue): self
    {
        return $this->setProperty('null_value', $nullValue);
    }
}
