<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class SemanticTextType extends AbstractField
{

    public Type $type = Type::semanticText;

    protected array $options = [
        'inference_id' => null,
        'search_inference_id' => null,
    ];

    public function inferenceId(string $value): self
    {
        $this->options['inference_id'] = $value;
        return $this;
    }

    public function searchInferenceId(string $value): self
    {
        $this->options['search_inference_id'] = $value;
        return $this;
    }

}