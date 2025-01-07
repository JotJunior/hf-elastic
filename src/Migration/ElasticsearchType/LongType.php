<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class LongType extends AbstractField
{

    public Type $type = Type::long;

}