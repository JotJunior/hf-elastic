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

    public function object(T\ObjectType $object): T\ObjectType
    {
        return $this->fields[] = $object;
    }

    public function getChildren(): array
    {
        return $this->fields;
    }

    public function nested(T\NestedType $nested): T\NestedType
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

    public function alias(string $name): T\AliasType
    {
        return $this->fields[] = new T\AliasType($name);
    }

    public function aggregateMetric(string $name): T\AggregateMetricType
    {
        return $this->fields[] = new T\AggregateMetricType($name);
    }

    public function binary(string $name): T\BinaryType
    {
        return $this->fields[] = new T\BinaryType($name);
    }

    public function boolean(string $name): T\BooleanType
    {
        return $this->fields[] = new T\BooleanType($name);
    }

    public function completion(string $name): T\CompletionType
    {
        return $this->fields[] = new T\CompletionType($name);
    }

    public function dateNanos(string $name): T\DateNanosType
    {
        return $this->fields[] = new T\DateNanosType($name);
    }

    public function dateRange(string $name): T\DateRangeTypeType
    {
        return $this->fields[] = new T\DateRangeTypeType($name);
    }

    public function date(string $name): T\DateType
    {
        return $this->fields[] = new T\DateType($name);
    }

    public function denseVector(string $name): T\DenseVectorType
    {
        return $this->fields[] = new T\DenseVectorType($name);
    }

    public function doubleRange(string $name): T\DoubleRangeTypeType
    {
        return $this->fields[] = new T\DoubleRangeTypeType($name);
    }

    public function double(string $name): T\DoubleType
    {
        return $this->fields[] = new T\DoubleType($name);
    }

    public function floatRange(string $name): T\FloatRangeTypeType
    {
        return $this->fields[] = new T\FloatRangeTypeType($name);
    }

    public function float(string $name): T\FloatType
    {
        return $this->fields[] = new T\FloatType($name);
    }

    public function geoPoint(string $name): T\GeoPointType
    {
        return $this->fields[] = new T\GeoPointType($name);
    }

    public function geoShape(string $name): T\GeoShapeType
    {
        return $this->fields[] = new T\GeoShapeType($name);
    }

    public function halfFloat(string $name): T\HalfFloatType
    {
        return $this->fields[] = new T\HalfFloatType($name);
    }

    public function histogram(string $name): T\HistogramType
    {
        return $this->fields[] = new T\HistogramType($name);
    }

    public function integerRange(string $name): T\IntegerRangeTypeType
    {
        return $this->fields[] = new T\IntegerRangeTypeType($name);
    }

    public function integer(string $name): T\IntegerType
    {
        return $this->fields[] = new T\IntegerType($name);
    }

    public function ip(string $name): T\IpType
    {
        return $this->fields[] = new T\IpType($name);
    }

    public function ipRange(string $name): T\IpRangeTypeType
    {
        return $this->fields[] = new T\IpRangeTypeType($name);
    }

    public function keyword(string $name): T\KeywordType
    {
        return $this->fields[] = new T\KeywordType($name);
    }

    public function longRange(string $name): T\LongRangeTypeType
    {
        return $this->fields[] = new T\LongRangeTypeType($name);
    }

    public function long(string $name): T\LongType
    {
        return $this->fields[] = new T\LongType($name);
    }

    public function numeric(string $name): T\Numeric
    {
        return $this->fields[] = new T\Numeric($name);
    }

    public function percolator(string $name): T\PercolatorType
    {
        return $this->fields[] = new T\PercolatorType($name);
    }

    public function point(string $name): T\PointType
    {
        return $this->fields[] = new T\PointType($name);
    }

    public function range(string $name): T\RangeType
    {
        return $this->fields[] = new T\RangeType($name);
    }

    public function rankFeature(string $name): T\RankFeatureType
    {
        return $this->fields[] = new T\RankFeatureType($name);
    }

    public function rankFeatures(string $name): T\RankFeatures
    {
        return $this->fields[] = new T\RankFeatures($name);
    }

    public function scaledFloat(string $name): T\ScaledFloatType
    {
        return $this->fields[] = new T\ScaledFloatType($name);
    }

    public function searchAsYou(string $name): T\SearchAsYouType
    {
        return $this->fields[] = new T\SearchAsYouType($name);
    }

    public function semanticText(string $name): T\SemanticTextType
    {
        return $this->fields[] = new T\SemanticTextType($name);
    }

    public function shape(string $name): T\ShapeType
    {
        return $this->fields[] = new T\ShapeType($name);
    }

    public function sparseVector(string $name): T\SparseVectorType
    {
        return $this->fields[] = new T\SparseVectorType($name);
    }

    public function text(string $name): T\TextType
    {
        return $this->fields[] = new T\TextType($name);
    }

    public function unsignedLong(string $name): T\UnsignedLongType
    {
        return $this->fields[] = new T\UnsignedLongType($name);
    }

    public function version(string $name): T\VersionType
    {
        return $this->fields[] = new T\VersionType($name);
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
        $this->fields[] = new T\DateNanosType('@timestamp');
    }


}