<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class DenseVector extends AbstractField
{

    public Type $type = Type::denseVector;


}