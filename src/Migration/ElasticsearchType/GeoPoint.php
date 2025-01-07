<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class GeoPoint extends AbstractField
{

    public Type $type = Type::geoPoint;

}