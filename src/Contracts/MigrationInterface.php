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

use Exception;
use Jot\HfElastic\Migration\Mapping;

interface MigrationInterface
{
    /**
     * Deletes the specified index from the Elasticsearch cluster.
     * @param string $indexName the name of the index to delete
     */
    public function delete(string $indexName): void;

    /**
     * Checks if the specified index exists.
     * @param string $indexName the name of the index to check
     * @return bool returns true if the index exists, false otherwise
     */
    public function exists(string $indexName): bool;

    /**
     * Creates a new index in the Elasticsearch cluster based on the provided mapping.
     * @param Mapping $index the mapping object containing the index configuration
     * @throws Exception if the index already exists
     */
    public function create(Mapping $index): void;

    /**
     * Updates the mapping of the specified index in the Elasticsearch cluster.
     * @param Mapping $index the index mapping object to update
     */
    public function update(Mapping $index): void;

    /**
     * Parses and returns the fully qualified index name by adding a prefix if necessary.
     * @param string $indexName the original name of the index to be parsed
     * @return string the parsed index name, including the prefix if applicable
     */
    public function parseIndexName(string $indexName): string;
}
