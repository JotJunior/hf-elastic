<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class IntegerType extends AbstractField
{

    public Type $type = Type::integer;

}