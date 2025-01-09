<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

enum Type
{
    case aggregateMetric;
    case binary;
    case boolean;
    case byte;
    case completion;
    case date;
    case dateRange;
    case dateNanos;
    case denseVector;
    case double;
    case doubleRange;
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
    case shape;
    case searchAsYouType;
    case semanticText;
    case shortRange;
    case short;
    case sparseVector;
    case text;
    case unsignedLong;
    case version;
}
