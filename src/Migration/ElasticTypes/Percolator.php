<?php

namespace Jot\HfElastic\Migration\ElasticTypes;

use Jot\HfElastic\Migration\AbstractField;

class Percolator extends AbstractField
{

    public Type $type = Type::percolator;

}