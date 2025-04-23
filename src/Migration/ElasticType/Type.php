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

enum Type
{
    case aggregateMetric;
    case aggregateMetricDouble;
    case alias;
    case array_object;
    case binary;
    case boolean;
    case byte;
    case completion;
    case date;
    case dateNanos;
    case dateRange;
    case denseVector;
    case double;
    case doubleRange;
    case dynamicObject;
    case flattened;
    case float;
    case floatRange;
    case geoPoint;
    case geoShape;
    case halfFloat;
    case histogram;
    case integer;
    case integerRange;
    case ip;
    case ipRange;
    case keyword;
    case long;
    case longRange;
    case nested;
    case object;
    case percolator;
    case point;
    case rankFeature;
    case rankFeatures;
    case scaledFloat;
    case searchAsYouType;
    case semanticText;
    case shape;
    case short;
    case shortRange;
    case sparseVector;
    case text;
    case unsignedLong;
    case version;
}
