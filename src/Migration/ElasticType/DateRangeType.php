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

class DateRangeType extends RangeType
{
    public Type $type = Type::dateRange;

    protected array $options = [
        'coerce' => null,
        'doc_values' => null,
        'format' => null,
        'index' => null,
        'store' => null,
    ];

    /**
     * Define o formato de data para o tipo date_range.
     * @param string $value O formato de data (ex: 'yyyy-MM-dd')
     */
    public function format(string $value): self
    {
        $this->options['format'] = $value;
        return $this;
    }
}
