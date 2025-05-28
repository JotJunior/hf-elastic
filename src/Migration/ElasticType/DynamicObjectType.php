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

class DynamicObjectType extends AbstractField
{
    public Type $type = Type::dynamicObject;

    protected array $options = [
        'dynamic' => true,
        'enabled' => null,
        'subobjects' => null,
        'properties' => null,
    ];

    public function dynamic(bool $value): self
    {
        $this->options['dynamic'] = $value;
        return $this;
    }

    public function enabled(bool $value): self
    {
        $this->options['enabled'] = $value;
        return $this;
    }

    public function subobjects(bool $value): self
    {
        $this->options['subobjects'] = $value;
        return $this;
    }
}
