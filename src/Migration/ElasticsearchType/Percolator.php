<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class Percolator extends AbstractField
{

    public Type $type = Type::percolator;

}