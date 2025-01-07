<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class GeoShape extends AbstractField
{

    public Type $type = Type::geoShape;

}