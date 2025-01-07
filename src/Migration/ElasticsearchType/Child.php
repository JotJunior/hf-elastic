<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;
use Jot\HfElastic\Migration\Property;

class Child extends Property
{

    public Type $type = Type::object;

}