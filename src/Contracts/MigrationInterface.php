<?php

namespace Jot\HfElastic\Contracts;

use Jot\HfElastic\Migration\Mapping;

interface MigrationInterface
{
    /**
     * Deletes the specified index from the Elasticsearch cluster.
     * @param string $indexName The name of the index to delete.
     * @return void
     */
    public function delete(string $indexName): void;

    /**
     * Checks if the specified index exists.
     * @param string $indexName The name of the index to check.
     * @return bool Returns true if the index exists, false otherwise.
     */
    public function exists(string $indexName): bool;

    /**
     * Creates a new index in the Elasticsearch cluster based on the provided mapping.
     * @param Mapping $index The mapping object containing the index configuration.
     * @return void
     * @throws \Exception If the index already exists.
     */
    public function create(Mapping $index): void;

    /**
     * Updates the mapping of the specified index in the Elasticsearch cluster.
     * @param Mapping $index The index mapping object to update.
     * @return void
     */
    public function update(Mapping $index): void;

    /**
     * Parses and returns the fully qualified index name by adding a prefix if necessary.
     * @param string $indexName The original name of the index to be parsed.
     * @return string The parsed index name, including the prefix if applicable.
     */
    public function parseIndexName(string $indexName): string;
}
