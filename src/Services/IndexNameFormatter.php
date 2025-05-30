<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Services;

use InvalidArgumentException;

class IndexNameFormatter
{
    private const INDEX_SEPARATOR = '_';

    private readonly string $indexPrefix;

    private ?string $indexSuffix;

    /**
     * @param string $prefix The index prefix (cannot be empty)
     * @param null|string $suffix The index suffix (optional)
     * @throws InvalidArgumentException when prefix is empty
     */
    public function __construct(string $prefix, ?string $suffix = null)
    {
        $this->indexPrefix = trim($prefix);
        $this->indexSuffix = $suffix !== null ? trim($suffix) : null;
    }

    /**
     * Formats the complete index name by applying prefix and/or suffix when necessary.
     */
    public function format(string $indexName): string
    {
        $formattedName = $indexName;

        if ($this->shouldPrefixIndex($indexName)) {
            $formattedName = $this->concatenateWithSeparator($this->indexPrefix, $formattedName);
        }

        if ($this->shouldSuffixIndex($formattedName)) {
            $formattedName = $this->concatenateWithSeparator($formattedName, $this->indexSuffix);
        }

        return $formattedName;
    }

    /**
     * Sets the suffix to be appended to the index name.
     * @param null|string $indexSuffix The suffix to be set for the index. If null, no suffix will be used.
     */
    public function setIndexSuffix(?string $indexSuffix): void
    {
        $this->indexSuffix = $indexSuffix;
    }

    /**
     * Determines whether the provided index name should be prefixed.
     * @param string $indexName the name of the index to be checked
     * @return bool returns true if the index name does not start with the defined prefix; otherwise, false
     */
    private function shouldPrefixIndex(string $indexName): bool
    {
        return ! str_starts_with($indexName, $this->indexPrefix);
    }

    /**
     * Concatenates two strings with a predefined separator.
     * @param string $first the first string to be concatenated
     * @param string $second the second string to be concatenated
     * @return string returns the concatenated result of the two strings separated by a predefined separator
     */
    private function concatenateWithSeparator(string $first, string $second): string
    {
        return sprintf('%s%s%s', $first, self::INDEX_SEPARATOR, $second);
    }

    /**
     * Determines whether the provided index name should be suffixed.
     * @param string $indexName the name of the index to be checked
     * @return bool returns true if the index name does not end with the defined suffix and the suffix is not null; otherwise, false
     */
    private function shouldSuffixIndex(string $indexName): bool
    {
        return $this->indexSuffix !== null && ! str_ends_with($indexName, $this->indexSuffix);
    }
}
