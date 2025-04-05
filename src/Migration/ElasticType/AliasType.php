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

class AliasType extends AbstractField
{
    public Type $type = Type::alias;

    protected array $options = [
        'path' => null,
    ];

    public function path(string $path): self
    {
        $this->options['path'] = $path;
        return $this;
    }
}
