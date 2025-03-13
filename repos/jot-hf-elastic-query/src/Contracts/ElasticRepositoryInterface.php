<?php

declare(strict_types=1);

namespace Jot\HfElasticQuery\Contracts;

/**
 * Interface for Elasticsearch repository.
 */
interface ElasticRepositoryInterface
{
    /**
     * Find a document by ID.
     *
     * @param string $index
     * @param string $id
     * @return array|null
     */
    public function find(string $index, string $id): ?array;

    /**
     * Find multiple documents by IDs.
     *
     * @param string $index
     * @param array $ids
     * @return array
     */
    public function findMany(string $index, array $ids): array;

    /**
     * Create a new document.
     *
     * @param string $index
     * @param array $document
     * @param string|null $id
     * @return array
     */
    public function create(string $index, array $document, ?string $id = null): array;

    /**
     * Update a document.
     *
     * @param string $index
     * @param string $id
     * @param array $document
     * @return array
     */
    public function update(string $index, string $id, array $document): array;

    /**
     * Delete a document.
     *
     * @param string $index
     * @param string $id
     * @return array
     */
    public function delete(string $index, string $id): array;

    /**
     * Bulk index documents.
     *
     * @param string $index
     * @param array $documents
     * @return array
     */
    public function bulkIndex(string $index, array $documents): array;

    /**
     * Bulk update documents.
     *
     * @param string $index
     * @param array $documents
     * @return array
     */
    public function bulkUpdate(string $index, array $documents): array;

    /**
     * Bulk delete documents.
     *
     * @param string $index
     * @param array $ids
     * @return array
     */
    public function bulkDelete(string $index, array $ids): array;

    /**
     * Search documents.
     *
     * @param array $params
     * @return array
     */
    public function search(array $params): array;

    /**
     * Count documents.
     *
     * @param array $params
     * @return int
     */
    public function count(array $params): int;

    /**
     * Execute a raw query.
     *
     * @param array $params
     * @return array
     */
    public function raw(array $params): array;
}
