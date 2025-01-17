<?php

namespace Jot\HfElastic\Migration;

use Jot\HfElastic\Migration\ElasticType as T;
use Jot\HfElastic\Migration\ElasticType\Type;

class Property
{

    protected Type $type = Type::object;
    protected FieldInterface $field;
    protected array $fields = [];
    protected array $options = [];

    public function __construct(protected string $name)
    {
        $this->name = $name;
    }

    public function child(T\ObjectType $child): T\ObjectType
    {
        return $this->fields[] = $child;
    }

    public function getChildren(): array
    {
        return $this->fields;
    }

    public function nested(T\Nested $nested): T\Nested
    {
        return $this->fields[] = $nested;
    }

    public function getOptions(): array
    {
        return array_filter($this->options);
    }

    public function getType(): T\Type
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function aggregateMetric(string $name): T\AggregateMetric
    {
        return $this->fields[] = new T\AggregateMetric($name);
    }

    public function binary(string $name): T\Binary
    {
        return $this->fields[] = new T\Binary($name);
    }

    public function boolean(string $name): T\BooleanType
    {
        return $this->fields[] = new T\BooleanType($name);
    }

    public function completion(string $name): T\Completion
    {
        return $this->fields[] = new T\Completion($name);
    }

    public function dateNanos(string $name): T\DateNanos
    {
        return $this->fields[] = new T\DateNanos($name);
    }

    public function dateRange(string $name): T\DateRange
    {
        return $this->fields[] = new T\DateRange($name);
    }

    public function date(string $name): T\DateType
    {
        return $this->fields[] = new T\DateType($name);
    }

    public function denseVector(string $name): T\DenseVector
    {
        return $this->fields[] = new T\DenseVector($name);
    }

    public function doubleRange(string $name): T\DoubleRange
    {
        return $this->fields[] = new T\DoubleRange($name);
    }

    public function double(string $name): T\DoubleType
    {
        return $this->fields[] = new T\DoubleType($name);
    }

    public function floatRange(string $name): T\FloatRange
    {
        return $this->fields[] = new T\FloatRange($name);
    }

    public function float(string $name): T\FloatType
    {
        return $this->fields[] = new T\FloatType($name);
    }

    public function geoPoint(string $name): T\GeoPoint
    {
        return $this->fields[] = new T\GeoPoint($name);
    }

    public function geoShape(string $name): T\GeoShape
    {
        return $this->fields[] = new T\GeoShape($name);
    }

    public function halfFloat(string $name): T\HalfFloatType
    {
        return $this->fields[] = new T\HalfFloatType($name);
    }

    public function histogram(string $name): T\Histogram
    {
        return $this->fields[] = new T\Histogram($name);
    }

    public function integerRange(string $name): T\IntegerRange
    {
        return $this->fields[] = new T\IntegerRange($name);
    }

    public function integer(string $name): T\IntegerType
    {
        return $this->fields[] = new T\IntegerType($name);
    }

    public function ip(string $name): T\Ip
    {
        return $this->fields[] = new T\Ip($name);
    }

    public function ipRange(string $name): T\IpRange
    {
        return $this->fields[] = new T\IpRange($name);
    }

    public function keyword(string $name): T\Keyword
    {
        return $this->fields[] = new T\Keyword($name);
    }

    public function longRange(string $name): T\LongRange
    {
        return $this->fields[] = new T\LongRange($name);
    }

    public function long(string $name): T\LongType
    {
        return $this->fields[] = new T\LongType($name);
    }

    public function numeric(string $name): T\Numeric
    {
        return $this->fields[] = new T\Numeric($name);
    }

    public function percolator(string $name): T\Percolator
    {
        return $this->fields[] = new T\Percolator($name);
    }

    public function point(string $name): T\Point
    {
        return $this->fields[] = new T\Point($name);
    }

    public function range(string $name): T\Range
    {
        return $this->fields[] = new T\Range($name);
    }

    public function rankFeature(string $name): T\RankFeature
    {
        return $this->fields[] = new T\RankFeature($name);
    }

    public function rankFeatures(string $name): T\RankFeatures
    {
        return $this->fields[] = new T\RankFeatures($name);
    }

    public function scaledFloat(string $name): T\ScaledFloat
    {
        return $this->fields[] = new T\ScaledFloat($name);
    }

    public function searchAsYou(string $name): T\SearchAsYouType
    {
        return $this->fields[] = new T\SearchAsYouType($name);
    }

    public function semanticText(string $name): T\SemanticText
    {
        return $this->fields[] = new T\SemanticText($name);
    }

    public function shape(string $name): T\Shape
    {
        return $this->fields[] = new T\Shape($name);
    }

    public function sparseVector(string $name): T\SparseVector
    {
        return $this->fields[] = new T\SparseVector($name);
    }

    public function text(string $name): T\TextType
    {
        return $this->fields[] = new T\TextType($name);
    }

    public function unsignedLong(string $name): T\UnsignedLong
    {
        return $this->fields[] = new T\UnsignedLong($name);
    }

    public function version(string $name): T\Version
    {
        return $this->fields[] = new T\Version($name);
    }

    /**
     * Defines the default fields for the entity, including standard timestamps,
     * version information, and deletion status.
     *
     * @return void
     */
    public function defaults(): void
    {
        $this->fields[] = new T\DateType('created_at');
        $this->fields[] = new T\DateType('updated_at');
        $this->fields[] = new T\BooleanType('deleted');
        $this->fields[] = new T\LongType('@version');
        $this->fields[] = new T\DateNanos('@timestamp');
    }


}