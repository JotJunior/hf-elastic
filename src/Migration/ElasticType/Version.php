<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class Version extends AbstractField
{

    public Type $type = Type::version;

}