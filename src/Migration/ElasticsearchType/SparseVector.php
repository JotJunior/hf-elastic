<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class SparseVector extends AbstractField
{

    public Type $type = Type::sparseVector;


}