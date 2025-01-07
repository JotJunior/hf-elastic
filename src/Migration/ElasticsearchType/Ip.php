<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class Ip extends AbstractField
{

    public Type $type = Type::ip;

}