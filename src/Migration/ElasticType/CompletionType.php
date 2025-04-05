<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class CompletionType extends AbstractField
{
    public Type $type = Type::completion;

    protected array $options = [
        'analyzer' => 'simple',
        'search_analyzer' => 'analyzer',
        'preserve_separators' => true,
        'preserve_position_increments' => true,
        'max_input_length' => 50,
    ];

    public function analyze(string $value): self
    {
        $this->options['analyzer'] = $value;
        return $this;
    }

    /**
     * Alias para analyze().
     */
    public function analyzer(string $value): self
    {
        return $this->analyze($value);
    }

    public function searchAnalyzer(string $value): self
    {
        $this->options['search_analyzer'] = $value;
        return $this;
    }

    public function preserveSeparators(bool $value): self
    {
        $this->options['preserve_separators'] = $value;
        return $this;
    }

    public function preservePositionsIncrements(bool $value): self
    {
        $this->options['preserve_position_increments'] = $value;
        return $this;
    }

    /**
     * Alias para preservePositionsIncrements().
     */
    public function preservePositionIncrements(bool $value): self
    {
        return $this->preservePositionsIncrements($value);
    }

    public function maxInputLength(int $value): self
    {
        $this->options['max_input_length'] = $value;
        return $this;
    }
}
