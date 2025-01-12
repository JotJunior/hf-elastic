<?php

namespace Jot\HfElastic\Migration\ElasticTypes;

use Jot\HfElastic\Migration\AbstractField;

class SparseVector extends AbstractField
{

    public Type $type = Type::sparseVector;


}