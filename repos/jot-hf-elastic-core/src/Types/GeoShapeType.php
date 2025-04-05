<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Types;

/**
 * GeoShape field type for Elasticsearch.
 * Used for complex shapes like polygons, lines, etc.
 */
class GeoShapeType extends AbstractElasticType
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'geo_shape');
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
     * Set the orientation parameter.
     *
     * @param string $orientation
     * @return self
     */
    public function setOrientation(string $orientation): self
    {
        return $this->setProperty('orientation', $orientation);
    }

    /**
     * Set the strategy parameter.
     *
     * @param string $strategy
     * @return self
     */
    public function setStrategy(string $strategy): self
    {
        return $this->setProperty('strategy', $strategy);
    }

    /**
     * Set the coerce parameter.
     *
     * @param bool $coerce
     * @return self
     */
    public function setCoerce(bool $coerce): self
    {
        return $this->setProperty('coerce', $coerce);
    }
}
