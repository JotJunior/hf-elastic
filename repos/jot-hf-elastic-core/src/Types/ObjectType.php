<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Types;

use Jot\HfElasticCore\Contracts\ElasticTypeInterface;

/**
 * Object field type for Elasticsearch.
 * Used for JSON objects.
 */
class ObjectType extends AbstractElasticType
{
    /**
     * Properties of the object.
     *
     * @var array<string, ElasticTypeInterface>
     */
    protected array $objectProperties = [];

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name, 'object');
    }

    /**
     * Add a property to the object.
     *
     * @param ElasticTypeInterface $property
     * @return self
     */
    public function addProperty(ElasticTypeInterface $property): self
    {
        $this->objectProperties[$property->getName()] = $property;

        return $this;
    }

    /**
     * Get all properties of the object.
     *
     * @return array<string, ElasticTypeInterface>
     */
    public function getProperties(): array
    {
        return $this->objectProperties;
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
     * Set the enabled parameter.
     *
     * @param bool $enabled
     * @return self
     */
    public function setEnabled(bool $enabled): self
    {
        return $this->setProperty('enabled', $enabled);
    }

    /**
     * Set the dynamic parameter.
     *
     * @param bool|string $dynamic
     * @return self
     */
    public function setDynamic(bool|string $dynamic): self
    {
        return $this->setProperty('dynamic', $dynamic);
    }

    /**
     * {@inheritdoc}
     */
    public function toMapping(): array
    {
        $mapping = parent::toMapping();

        if (!empty($this->objectProperties)) {
            $properties = [];

            foreach ($this->objectProperties as $property) {
                $properties[$property->getName()] = $property->toMapping();
            }

            $mapping['properties'] = $properties;
        }

        return $mapping;
    }
}
