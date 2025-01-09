<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class Histogram extends AbstractField
{

    public Type $type = Type::histogram;

}