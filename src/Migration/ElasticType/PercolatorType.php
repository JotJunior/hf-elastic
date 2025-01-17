<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class PercolatorType extends AbstractField
{

    public Type $type = Type::percolator;

}