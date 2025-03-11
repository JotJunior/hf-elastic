<?php

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
     * Alias para analyze()
     * 
     * @param string $value
     * @return self
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
     * Alias para preservePositionsIncrements()
     * 
     * @param bool $value
     * @return self
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