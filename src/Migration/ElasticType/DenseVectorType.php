<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class DenseVectorType extends AbstractField
{

    public Type $type = Type::denseVector;
    
    protected array $options = [
        'dims' => null,
        'similarity' => null,
    ];
    
    /**
     * Construtor para o tipo dense_vector
     * @param string $name Nome do campo
     * @param int|null $dimensions Número de dimensões do vetor
     */
    public function __construct(string $name, ?int $dimensions = null)
    {
        parent::__construct($name);
        
        // Sempre definir o valor de dims, mesmo que seja null
        $this->options['dims'] = $dimensions;
    }
    
    /**
     * Define a medida de similaridade para o tipo dense_vector
     * @param string $value Medida de similaridade (ex: 'cosine', 'dot_product', 'l2_norm')
     * @return self
     */
    public function similarity(string $value): self
    {
        $this->options['similarity'] = $value;
        return $this;
    }
    
    /**
     * Define o número de dimensões para o tipo dense_vector
     * @param int $value Número de dimensões
     * @return self
     */
    public function dimensions(int $value): self
    {
        $this->options['dims'] = $value;
        return $this;
    }
}
