<?php

namespace Jot\HfElastic\Migration;

use Jot\HfElastic\Migration\ElasticType\Type;

class AbstractField implements FieldInterface
{
    public Type $type;
    protected string $name;
    protected array $options = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function options(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOptions(): array
    {
        // Filtrar apenas valores null, mas manter valores como false, 0, '', etc.
        return array_filter($this->options, function($value) {
            return $value !== null;
        });
    }

    public function getType(): Type
    {
        return $this->type;
    }
}
