<?php

namespace Jot\HfElastic\Migration\ElasticTypes;

use Jot\HfElastic\Migration\AbstractField;

class Completion extends AbstractField
{

    public Type $type = Type::completion;

    protected array $options = [
        'analyzer' => 'simple',
        'search_analyzer' => 'analyzer',
        'preserve_separators' => true,
        'preserve_positions_increments' => true,
        'max_input_length' => 50,
    ];

    public function analyze(string $value): self
    {
        $this->options['analyzer'] = $value;
        return $this;
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
        $this->options['preserve_positions_increments'] = $value;
        return $this;
    }

    public function maxInputLength(int $value): self
    {
        $this->options['max_input_length'] = $value;
        return $this;
    }

}