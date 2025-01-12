<?php

namespace Jot\HfElastic\Migration\ElasticTypes;

use Jot\HfElastic\Migration\AbstractField;

class DenseVector extends AbstractField
{

    public Type $type = Type::denseVector;


}