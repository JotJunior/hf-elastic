<?php

namespace Jot\HfElastic\Factories;

use Jot\HfElastic\Contracts\PropertyInterface;
use Jot\HfElastic\Exception\UnsupportedTypeException;
use Jot\HfElastic\Migration\ElasticType as T;
use Jot\HfElastic\Migration\FieldInterface;

/**
 * Factory for creating Elasticsearch field type instances.
 * This factory supports all available field types in Elasticsearch
 * and facilitates the creation of instances based on the type name.
 */
class FieldTypeFactory
{
    /**
     * Creates an instance of an Elasticsearch field type.
     * @param string $type Field type name (e.g., 'text', 'keyword', 'integer', etc.)
     * @param string $name Field name
     * @param array|null $params Additional parameters for specific types
     * @return FieldInterface|PropertyInterface Field type instance
     * @throws \InvalidArgumentException If the type is not recognized
     */
    public function create(string $type, string $name, ?array $params = null): FieldInterface|PropertyInterface
    {
        return match ($type) {
            // Text types
            'text' => new T\TextType($name),
            'keyword' => new T\KeywordType($name),
            'search_as_you_type' => new T\SearchAsYouType($name),
            'semantic_text' => new T\SemanticTextType($name),

            // Numeric types
            'long' => new T\LongType($name),
            'integer' => new T\IntegerType($name),
            'short' => new T\IntegerType($name), // Elasticsearch doesn't have a specific ShortType
            'byte' => new T\IntegerType($name), // Elasticsearch doesn't have a specific ByteType
            'double' => new T\DoubleType($name),
            'float' => new T\FloatType($name),
            'half_float' => new T\HalfFloatType($name),
            'scaled_float' => new T\ScaledFloatType($name, $params['scaling_factor'] ?? 1.0),
            'unsigned_long' => new T\UnsignedLongType($name),

            // Date types
            'date' => new T\DateType($name),
            'time' => new T\TimeType($name),
            'date_nanos' => new T\DateNanosType($name),

            // Boolean types
            'boolean' => new T\BooleanType($name),

            // Binary types
            'binary' => new T\BinaryType($name),

            // Range types
            'integer_range' => new T\IntegerRangeType($name),
            'float_range' => new T\FloatRangeTypeType($name),
            'long_range' => new T\LongRangeType($name),
            'double_range' => new T\DoubleRangeTypeType($name),
            'date_range' => new T\DateRangeType($name),
            'ip_range' => new T\IpRangeTypeType($name),
            'range' => new T\RangeType($name),

            // Complex types
            'object' => new T\ObjectType($name),
            'nested' => new T\NestedType($name),

            // Geospatial types
            'geo_point' => new T\GeoPointType($name),
            'geo_shape' => new T\GeoShapeType($name),
            'point' => new T\PointType($name),
            'shape' => new T\ShapeType($name),

            // Specialized types
            'ip' => new T\IpType($name),
            'completion' => new T\CompletionType($name),
            'alias' => new T\AliasType($name),
            'percolator' => new T\PercolatorType($name),
            'rank_feature' => new T\RankFeatureType($name),
            'rank_features' => new T\RankFeaturesType($name),
            'version' => new T\VersionType($name),
            'dense_vector' => new T\DenseVectorType($name, $params['dims'] ?? null),
            'sparse_vector' => new T\SparseVectorType($name),
            'histogram' => new T\HistogramType($name),
            'aggregate_metric' => new T\AggregateMetricType($name),
            'aggregate_metric_double' => new T\AggregateMetricDoubleType($name),

            // Default case for unrecognized types
            default => throw new UnsupportedTypeException($type)
        };
    }
}
