<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class SparseVectorType extends AbstractField
{

    public Type $type = Type::sparseVector;


}
