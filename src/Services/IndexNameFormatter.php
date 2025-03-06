<?php

declare(strict_types=1);

namespace Jot\HfElastic\Services;

use Hyperf\Contract\ConfigInterface;

/**
 * Service for formatting index names with appropriate prefixes.
 */
class IndexNameFormatter
{
    private string $indexPrefix;
    
    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->indexPrefix = $config->get('hf_elastic')['prefix'] ?? '';
    }
    
    /**
     * Generates the full index name by appending a prefix if it is set.
     *
     * @param string $indexName The base name of the index.
     * @return string The full index name, including the prefix if applicable.
     */
    public function format(string $indexName): string
    {
        if ($this->shouldPrefixIndex($indexName)) {
            return sprintf('%s_%s', $this->indexPrefix, $indexName);
        }
        return $indexName;
    }
    
    /**
     * Determines if an index name should be prefixed.
     *
     * @param string $indexName The index name to check.
     * @return bool True if the index should be prefixed, false otherwise.
     */
    private function shouldPrefixIndex(string $indexName): bool
    {
        return $this->indexPrefix !== '' && !str_starts_with($indexName, $this->indexPrefix);
    }
}
