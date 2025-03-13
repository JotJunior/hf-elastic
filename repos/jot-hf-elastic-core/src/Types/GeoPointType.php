<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Types;

/**
 * GeoPoint field type for Elasticsearch.
 * Used for latitude/longitude points.
 */
class GeoPointType extends AbstractElasticType
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'geo_point');
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
     * Set the ignore_malformed parameter.
     *
     * @param bool $ignoreMalformed
     * @return self
     */
    public function setIgnoreMalformed(bool $ignoreMalformed): self
    {
        return $this->setProperty('ignore_malformed', $ignoreMalformed);
    }

    /**
     * Set the ignore_z_value parameter.
     *
     * @param bool $ignoreZValue
     * @return self
     */
    public function setIgnoreZValue(bool $ignoreZValue): self
    {
        return $this->setProperty('ignore_z_value', $ignoreZValue);
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
