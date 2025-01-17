<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class BinaryType extends AbstractField
{

    public Type $type = Type::boolean;

}