<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class DateType extends AbstractField
{

    public Type $type = Type::date;

}