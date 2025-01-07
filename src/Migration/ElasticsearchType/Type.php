<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

enum Type
{
    case keyword;
    case text;
    case integer;
    case long;
    case float;
    case double;
    case boolean;
    case date;
    case ip;
    case geoPoint;
    case geoShape;
    case nested;
    case object;
}
