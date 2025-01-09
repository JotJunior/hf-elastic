<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class Binary extends AbstractField
{

    public Type $type = Type::boolean;

}