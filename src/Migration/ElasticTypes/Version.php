<?php

namespace Jot\HfElastic\Migration\ElasticTypes;

use Jot\HfElastic\Migration\AbstractField;

class Version extends AbstractField
{

    public Type $type = Type::version;

}