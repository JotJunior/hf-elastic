<?php

declare(strict_types=1);

namespace Jot\HfElastic\Contracts;

/**
 * Interface for Elasticsearch repository operations.
 */
interface ElasticRepositoryInterface
{
    /**
     * Inserts a new document into the specified index.
     * @param array $data The data to be inserted.
     * @return array An associative array containing the result status, any errors encountered, and the inserted data.
     */
    public function insert(array $data): array;
    
    /**
     * Updates a document by its identifier with the provided data.
     * @param string $id The unique identifier of the document to update.
     * @param array $data The data to update the document with.
     * @return array Returns an array containing the result of the operation.
     */
    public function update(string $id, array $data): array;
    
    /**
     * Deletes a document either logically or physically based on the given parameters.
     * @param string $id The unique identifier of the document to delete.
     * @param bool $logicalDeletion Determines if the deletion should be logical or physical.
     * @return array Returns an array containing the result of the operation.
     */
    public function delete(string $id, bool $logicalDeletion = true): array;
    
    /**
     * Performs a bulk update operation on documents matching the specified query.
     * @param array $data The data to update within the matched documents.
     * @return array Returns an associative array containing the count of updated documents and their IDs.
     */
    public function bulkUpdate(array $data): array;
    
    /**
     * Performs a bulk delete operation on documents matching the specified query.
     * @param bool $logicalDeletion Determines whether the documents are logically or physically deleted.
     * @return array Returns an associative array containing the count of deleted documents and their IDs.
     */
    public function bulkDelete(bool $logicalDeletion = true): array;
    
    /**
     * Retrieves the version number of a document based on its identifier.
     * @param string $id The unique identifier of the document.
     * @return int|null Returns the version number of the document if found, or null if not.
     */
    public function getDocumentVersion(string $id): ?int;
}
