<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Contracts;

/**
 * Interface for Elasticsearch client wrapper.
 */
interface ElasticClientInterface
{
    /**
     * Create an index with the given name and settings.
     *
     * @param string $index
     * @param array $settings
     * @return array
     */
    public function createIndex(string $index, array $settings = []): array;

    /**
     * Delete an index with the given name.
     *
     * @param string $index
     * @return array
     */
    public function deleteIndex(string $index): array;

    /**
     * Check if an index exists.
     *
     * @param string $index
     * @return bool
     */
    public function indexExists(string $index): bool;

    /**
     * Get index settings.
     *
     * @param string $index
     * @return array
     */
    public function getIndexSettings(string $index): array;

    /**
     * Update index mapping.
     *
     * @param string $index
     * @param array $mapping
     * @return array
     */
    public function updateMapping(string $index, array $mapping): array;

    /**
     * Get index mapping.
     *
     * @param string $index
     * @return array
     */
    public function getMapping(string $index): array;

    /**
     * Index a document.
     *
     * @param string $index
     * @param array $document
     * @param string|null $id
     * @return array
     */
    public function index(string $index, array $document, ?string $id = null): array;

    /**
     * Bulk index documents.
     *
     * @param array $params
     * @return array
     */
    public function bulk(array $params): array;

    /**
     * Search documents.
     *
     * @param array $params
     * @return array
     */
    public function search(array $params): array;

    /**
     * Get a document by ID.
     *
     * @param string $index
     * @param string $id
     * @return array
     */
    public function get(string $index, string $id): array;

    /**
     * Delete a document by ID.
     *
     * @param string $index
     * @param string $id
     * @return array
     */
    public function delete(string $index, string $id): array;

    /**
     * Update a document by ID.
     *
     * @param string $index
     * @param string $id
     * @param array $body
     * @return array
     */
    public function update(string $index, string $id, array $body): array;
}
