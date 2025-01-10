<?php

namespace Jot\HfElastic\Migration;

use Jot\HfElastic\Migration\ElasticsearchType\BooleanType;
use Jot\HfElastic\Migration\ElasticsearchType\ObjectType;
use Jot\HfElastic\Migration\ElasticsearchType\DateType;
use Jot\HfElastic\Migration\ElasticsearchType\DoubleType;
use Jot\HfElastic\Migration\ElasticsearchType\FloatType;
use Jot\HfElastic\Migration\ElasticsearchType\GeoPoint;
use Jot\HfElastic\Migration\ElasticsearchType\GeoShape;
use Jot\HfElastic\Migration\ElasticsearchType\IntegerType;
use Jot\HfElastic\Migration\ElasticsearchType\Ip;
use Jot\HfElastic\Migration\ElasticsearchType\Keyword;
use Jot\HfElastic\Migration\ElasticsearchType\LongType;
use Jot\HfElastic\Migration\ElasticsearchType\Nested;
use Jot\HfElastic\Migration\ElasticsearchType\TextType;
use Jot\HfElastic\Migration\ElasticsearchType\Type;

class Property
{
    protected string $name;
    protected Type $type = Type::object;
    protected FieldInterface $field;
    protected array $fields = [];
    protected array $options = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function boolean(string $name): BooleanType
    {
        return $this->fields[] = new BooleanType($name);
    }

    public function child(ObjectType $child): ObjectType
    {
        return $this->fields[] = $child;
    }

    public function date(string $name): DateType
    {
        return $this->fields[] = new DateType($name);
    }

    public function double(string $name): DoubleType
    {
        return $this->fields[] = new DoubleType($name);
    }

    public function float(string $name): FloatType
    {
        return $this->fields[] = new FloatType($name);
    }

    public function geoPoint(string $name): GeoPoint
    {
        return $this->fields[] = new GeoPoint($name);
    }

    public function geoShape(string $name): GeoShape
    {
        return $this->fields[] = new GeoShape($name);
    }

    public function integer(string $name): IntegerType
    {
        return $this->fields[] = new IntegerType($name);
    }

    public function ip(string $name): Ip
    {
        return $this->fields[] = new Ip($name);
    }

    public function keyword(string $name): Keyword
    {
        return $this->fields[] = new Keyword($name);
    }

    public function long(string $name): LongType
    {
        return $this->fields[] = new LongType($name);
    }

    public function nested(Nested $nested): Nested
    {
        return $this->fields[] = $nested;
    }

    public function text(string $name): TextType
    {
        return $this->fields[] = new TextType($name);
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getChildren(): array
    {
        return $this->fields;
    }

    public function getOptions(): array
    {
        return array_filter($this->options);
    }

}