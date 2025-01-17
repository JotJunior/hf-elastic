<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class HistogramType extends AbstractField
{

    public Type $type = Type::histogram;

}