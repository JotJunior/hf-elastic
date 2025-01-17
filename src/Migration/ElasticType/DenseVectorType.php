<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class DenseVectorType extends AbstractField
{

    public Type $type = Type::denseVector;


}