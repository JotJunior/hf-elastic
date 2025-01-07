<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class DoubleType extends AbstractField
{

    public Type $type = Type::double;

}