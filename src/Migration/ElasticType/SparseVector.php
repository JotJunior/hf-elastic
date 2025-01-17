<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class SparseVector extends AbstractField
{

    public Type $type = Type::sparseVector;


}