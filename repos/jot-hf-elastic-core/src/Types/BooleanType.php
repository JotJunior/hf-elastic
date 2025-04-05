<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Types;

/**
 * Boolean field type for Elasticsearch.
 * Used for boolean values (true/false).
 */
class BooleanType extends AbstractElasticType
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'boolean');
        $this->filterable = true;
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
     * Set the null_value parameter.
     *
     * @param bool $nullValue
     * @return self
     */
    public function setNullValue(bool $nullValue): self
    {
        return $this->setProperty('null_value', $nullValue);
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

    /**
     * Set the index parameter.
     *
     * @param bool $index
     * @return self
     */
    public function setIndex(bool $index): self
    {
        return $this->setProperty('index', $index);
    }
}
