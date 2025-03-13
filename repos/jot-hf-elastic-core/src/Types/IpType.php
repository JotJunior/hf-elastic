<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Types;

/**
 * IP field type for Elasticsearch.
 * Used for IPv4 and IPv6 addresses.
 */
class IpType extends AbstractElasticType
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'ip');
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
     * Set the null_value parameter.
     *
     * @param string $nullValue
     * @return self
     */
    public function setNullValue(string $nullValue): self
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

    /**
     * Set the ignore_malformed parameter.
     *
     * @param bool $ignoreMalformed
     * @return self
     */
    public function setIgnoreMalformed(bool $ignoreMalformed): self
    {
        return $this->setProperty('ignore_malformed', $ignoreMalformed);
    }
}
