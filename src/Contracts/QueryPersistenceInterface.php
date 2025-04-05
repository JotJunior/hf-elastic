<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Contracts;

/**
 * Interface for building Elasticsearch queries.
 */
interface QueryPersistenceInterface
{
    /**
     * Counts the number of documents in the index matching the specified query.
     * @return int returns the total number of documents that satisfy the query criteria
     */
    public function count(): int;

    /**
     * Executes a search query on the specified index and retrieves matching results.
     * @return array returns an array of search hits retrieved from the query execution
     */
    public function execute(): array;

    /**
     * Deletes a resource by its identifier, with an option for logical or physical deletion.
     * @param string $id the unique identifier of the resource to be deleted
     * @param bool $logicalDeletion Determines if the deletion should be logical (true) or physical (false). Default is true.
     * @return array returns an array containing the result of the deletion process
     */
    public function delete(string $id, bool $logicalDeletion = true): array;

    /**
     * Updates a record with the given data based on the provided identifier.
     * @param string $id the unique identifier of the record to be updated
     * @param array $data the data to be updated for the specified record
     * @return array the updated record after the operation
     */
    public function update(string $id, array $data): array;

    /**
     * Inserts the provided data into the database or data structure.
     * @param array $data the data to be inserted
     * @return array the result of the insert operation, typically the inserted data or status information
     */
    public function insert(array $data): array;

    /**
     * Retrieves the version number of the specified document.
     * @param string $id the unique identifier of the document
     * @return null|int the version number of the document, or null if the document does not exist
     */
    public function getDocumentVersion(string $id): ?int;
}
