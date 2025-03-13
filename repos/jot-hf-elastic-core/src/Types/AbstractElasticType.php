<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Types;

use Jot\HfElasticCore\Contracts\ElasticTypeInterface;

/**
 * Abstract base class for Elasticsearch field types.
 */
abstract class AbstractElasticType implements ElasticTypeInterface
{
    /**
     * Field name.
     *
     * @var string
     */
    protected string $name;

    /**
     * Field type.
     *
     * @var string
     */
    protected string $type;

    /**
     * Whether the field is searchable.
     *
     * @var bool
     */
    protected bool $searchable = false;

    /**
     * Whether the field is filterable.
     *
     * @var bool
     */
    protected bool $filterable = false;

    /**
     * Whether the field is sortable.
     *
     * @var bool
     */
    protected bool $sortable = false;

    /**
     * Additional field properties.
     *
     * @var array
     */
    protected array $properties = [];

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $type
     */
    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): ElasticTypeInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setSearchable(bool $searchable = true): ElasticTypeInterface
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterable(bool $filterable = true): ElasticTypeInterface
    {
        $this->filterable = $filterable;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isFilterable(): bool
    {
        return $this->filterable;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortable(bool $sortable = true): ElasticTypeInterface
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Set a property value.
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function setProperty(string $name, mixed $value): self
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * Get a property value.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getProperty(string $name, mixed $default = null): mixed
    {
        return $this->properties[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function toMapping(): array
    {
        $mapping = [
            'type' => $this->type,
        ];

        return array_merge($mapping, $this->properties);
    }
}
