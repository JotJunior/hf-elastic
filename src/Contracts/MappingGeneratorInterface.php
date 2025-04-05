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
 * Interface for classes that generate Elasticsearch mapping code from different sources.
 */
interface MappingGeneratorInterface
{
    /**
     * String representation of the generator output.
     * @return string the generated mapping code
     */
    public function __toString();

    /**
     * Generates the mapping body code.
     * @param string $var variable name to use in the generated code
     * @param array $data optional data to use instead of the internal data
     * @return string the generated mapping code
     */
    public function body(string $var = 'index', array $data = []): string;
}
