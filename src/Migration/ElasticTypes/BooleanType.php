<?php

namespace Jot\HfElastic\Migration\ElasticTypes;

use Jot\HfElastic\Migration\AbstractField;

class BooleanType extends AbstractField
{

    public Type $type = Type::boolean;

}