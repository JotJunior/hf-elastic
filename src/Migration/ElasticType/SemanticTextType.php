<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class SemanticTextType extends AbstractField
{

    public Type $type = Type::semanticText;

    protected array $options = [
        'inference_id' => null,
        'search_inference_id' => null,
        'model_id' => null,
        'dimensions' => null,
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
    
    /**
     * Define o ID do modelo para o tipo semantic_text
     * @param string $value
     * @return self
     */
    public function modelId(string $value): self
    {
        $this->options['model_id'] = $value;
        return $this;
    }
    
    /**
     * Define o número de dimensões para o tipo semantic_text
     * @param int $value
     * @return self
     */
    public function dimensions(int $value): self
    {
        $this->options['dimensions'] = $value;
        return $this;
    }

}
