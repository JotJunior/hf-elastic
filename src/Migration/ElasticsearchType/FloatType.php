<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class FloatType extends AbstractField
{

    public Type $type = Type::double;

}