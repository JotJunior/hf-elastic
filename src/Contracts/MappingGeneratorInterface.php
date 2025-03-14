<?php

namespace Jot\HfElastic\Contracts;

/**
 * Interface for classes that generate Elasticsearch mapping code from different sources.
 */
interface MappingGeneratorInterface
{
    /**
     * Generates the mapping body code.
     * @param string $var Variable name to use in the generated code.
     * @param array $data Optional data to use instead of the internal data.
     * @return string The generated mapping code.
     */
    public function body(string $var = 'index', array $data = []): string;
    
    /**
     * String representation of the generator output.
     * @return string The generated mapping code.
     */
    public function __toString();
}
