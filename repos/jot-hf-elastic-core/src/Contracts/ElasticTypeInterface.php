<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Contracts;

/**
 * Interface for Elasticsearch field types.
 */
interface ElasticTypeInterface
{
    /**
     * Get the Elasticsearch type mapping.
     *
     * @return array
     */
    public function toMapping(): array;

    /**
     * Get the field name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set the field name.
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self;

    /**
     * Get the field type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Set field as searchable.
     *
     * @param bool $searchable
     * @return self
     */
    public function setSearchable(bool $searchable = true): self;

    /**
     * Check if field is searchable.
     *
     * @return bool
     */
    public function isSearchable(): bool;

    /**
     * Set field as filterable.
     *
     * @param bool $filterable
     * @return self
     */
    public function setFilterable(bool $filterable = true): self;

    /**
     * Check if field is filterable.
     *
     * @return bool
     */
    public function isFilterable(): bool;

    /**
     * Set field as sortable.
     *
     * @param bool $sortable
     * @return self
     */
    public function setSortable(bool $sortable = true): self;

    /**
     * Check if field is sortable.
     *
     * @return bool
     */
    public function isSortable(): bool;
}
