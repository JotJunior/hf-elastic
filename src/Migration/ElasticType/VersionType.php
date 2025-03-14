<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class VersionType extends AbstractField
{

    public Type $type = Type::version;

}
