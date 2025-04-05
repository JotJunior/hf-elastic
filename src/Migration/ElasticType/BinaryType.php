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

class BinaryType extends AbstractField
{
    public Type $type = Type::binary;

    protected array $options = [
        'doc_values' => null,
        'store' => null,
    ];

    /**
     * Define se o campo deve ser armazenado em doc_values.
     */
    public function docValues(bool $value): self
    {
        $this->options['doc_values'] = $value;
        return $this;
    }

    /**
     * Define se o campo deve ser armazenado.
     */
    public function store(bool $value): self
    {
        $this->options['store'] = $value;
        return $this;
    }
}
