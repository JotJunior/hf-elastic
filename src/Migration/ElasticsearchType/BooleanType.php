<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class BooleanType extends AbstractField
{

    public Type $type = Type::boolean;

}