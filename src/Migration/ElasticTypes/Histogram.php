<?php

namespace Jot\HfElastic\Migration\ElasticTypes;

use Jot\HfElastic\Migration\AbstractField;

class Histogram extends AbstractField
{

    public Type $type = Type::histogram;

}