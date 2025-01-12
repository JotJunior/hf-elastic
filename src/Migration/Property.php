<?php

namespace Jot\HfElastic\Migration;

use Jot\HfElastic\Migration\ElasticTypes\AggregateMetric;
use Jot\HfElastic\Migration\ElasticTypes\Binary;
use Jot\HfElastic\Migration\ElasticTypes\BooleanType;
use Jot\HfElastic\Migration\ElasticTypes\Completion;
use Jot\HfElastic\Migration\ElasticTypes\DateNanos;
use Jot\HfElastic\Migration\ElasticTypes\DateRange;
use Jot\HfElastic\Migration\ElasticTypes\DateType;
use Jot\HfElastic\Migration\ElasticTypes\DenseVector;
use Jot\HfElastic\Migration\ElasticTypes\DoubleRange;
use Jot\HfElastic\Migration\ElasticTypes\DoubleType;
use Jot\HfElastic\Migration\ElasticTypes\FloatRange;
use Jot\HfElastic\Migration\ElasticTypes\FloatType;
use Jot\HfElastic\Migration\ElasticTypes\GeoPoint;
use Jot\HfElastic\Migration\ElasticTypes\GeoShape;
use Jot\HfElastic\Migration\ElasticTypes\HalfFloatType;
use Jot\HfElastic\Migration\ElasticTypes\Histogram;
use Jot\HfElastic\Migration\ElasticTypes\IntegerRange;
use Jot\HfElastic\Migration\ElasticTypes\IntegerType;
use Jot\HfElastic\Migration\ElasticTypes\Ip;
use Jot\HfElastic\Migration\ElasticTypes\IpRange;
use Jot\HfElastic\Migration\ElasticTypes\Keyword;
use Jot\HfElastic\Migration\ElasticTypes\LongRange;
use Jot\HfElastic\Migration\ElasticTypes\LongType;
use Jot\HfElastic\Migration\ElasticTypes\Nested;
use Jot\HfElastic\Migration\ElasticTypes\Numeric;
use Jot\HfElastic\Migration\ElasticTypes\ObjectType;
use Jot\HfElastic\Migration\ElasticTypes\Percolator;
use Jot\HfElastic\Migration\ElasticTypes\Point;
use Jot\HfElastic\Migration\ElasticTypes\Range;
use Jot\HfElastic\Migration\ElasticTypes\RankFeature;
use Jot\HfElastic\Migration\ElasticTypes\RankFeatures;
use Jot\HfElastic\Migration\ElasticTypes\ScaledFloat;
use Jot\HfElastic\Migration\ElasticTypes\SearchAsYouType;
use Jot\HfElastic\Migration\ElasticTypes\SemanticText;
use Jot\HfElastic\Migration\ElasticTypes\Shape;
use Jot\HfElastic\Migration\ElasticTypes\SparseVector;
use Jot\HfElastic\Migration\ElasticTypes\TextType;
use Jot\HfElastic\Migration\ElasticTypes\Type;
use Jot\HfElastic\Migration\ElasticTypes\UnsignedLong;
use Jot\HfElastic\Migration\ElasticTypes\Version;

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

    public function child(ObjectType $child): ObjectType
    {
        return $this->fields[] = $child;
    }

    public function getChildren(): array
    {
        return $this->fields;
    }

    public function nested(Nested $nested): Nested
    {
        return $this->fields[] = $nested;
    }

    public function getOptions(): array
    {
        return array_filter($this->options);
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function aggregateMetric(string $name): AggregateMetric
    {
        return $this->fields[] = new AggregateMetric($name);
    }

    public function binary(string $name): Binary
    {
        return $this->fields[] = new Binary($name);
    }

    public function boolean(string $name): BooleanType
    {
        return $this->fields[] = new BooleanType($name);
    }

    public function completion(string $name): Completion
    {
        return $this->fields[] = new Completion($name);
    }

    public function dateNanos(string $name): DateNanos
    {
        return $this->fields[] = new DateNanos($name);
    }

    public function dateRange(string $name): DateRange
    {
        return $this->fields[] = new DateRange($name);
    }

    public function date(string $name): DateType
    {
        return $this->fields[] = new DateType($name);
    }

    public function denseVector(string $name): DenseVector
    {
        return $this->fields[] = new DenseVector($name);
    }

    public function doubleRange(string $name): DoubleRange
    {
        return $this->fields[] = new DoubleRange($name);
    }

    public function double(string $name): DoubleType
    {
        return $this->fields[] = new DoubleType($name);
    }

    public function floatRange(string $name): FloatRange
    {
        return $this->fields[] = new FloatRange($name);
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

    public function halfFloat(string $name): HalfFloatType
    {
        return $this->fields[] = new HalfFloatType($name);
    }

    public function histogram(string $name): Histogram
    {
        return $this->fields[] = new Histogram($name);
    }

    public function integerRange(string $name): IntegerRange
    {
        return $this->fields[] = new IntegerRange($name);
    }

    public function integer(string $name): IntegerType
    {
        return $this->fields[] = new IntegerType($name);
    }

    public function ip(string $name): Ip
    {
        return $this->fields[] = new Ip($name);
    }

    public function ipRange(string $name): IpRange
    {
        return $this->fields[] = new IpRange($name);
    }

    public function keyword(string $name): Keyword
    {
        return $this->fields[] = new Keyword($name);
    }

    public function longRange(string $name): LongRange
    {
        return $this->fields[] = new LongRange($name);
    }

    public function long(string $name): LongType
    {
        return $this->fields[] = new LongType($name);
    }

    public function numeric(string $name): Numeric
    {
        return $this->fields[] = new Numeric($name);
    }

    public function percolator(string $name): Percolator
    {
        return $this->fields[] = new Percolator($name);
    }

    public function point(string $name): Point
    {
        return $this->fields[] = new Point($name);
    }

    public function range(string $name): Range
    {
        return $this->fields[] = new Range($name);
    }

    public function rankFeature(string $name): RankFeature
    {
        return $this->fields[] = new RankFeature($name);
    }

    public function rankFeatures(string $name): RankFeatures
    {
        return $this->fields[] = new RankFeatures($name);
    }

    public function scaledFloat(string $name): ScaledFloat
    {
        return $this->fields[] = new ScaledFloat($name);
    }

    public function searchAsYou(string $name): SearchAsYouType
    {
        return $this->fields[] = new SearchAsYouType($name);
    }

    public function semanticText(string $name): SemanticText
    {
        return $this->fields[] = new SemanticText($name);
    }

    public function shape(string $name): Shape
    {
        return $this->fields[] = new Shape($name);
    }

    public function sparseVector(string $name): SparseVector
    {
        return $this->fields[] = new SparseVector($name);
    }

    public function text(string $name): TextType
    {
        return $this->fields[] = new TextType($name);
    }

    public function unsignedLong(string $name): UnsignedLong
    {
        return $this->fields[] = new UnsignedLong($name);
    }

    public function version(string $name): Version
    {
        return $this->fields[] = new Version($name);
    }

    /**
     * Defines the default fields for the entity, including standard timestamps,
     * version information, and deletion status.
     *
     * @return void
     */
    public function defaults(): void
    {
        $this->fields[] = new DateType('created_at');
        $this->fields[] = new DateType('updated_at');
        $this->fields[] = new BooleanType('deleted');
        $this->fields[] = new LongType('@version');
        $this->fields[] = new DateNanos('@timestamp');
    }


}