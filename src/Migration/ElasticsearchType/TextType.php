<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class TextType extends AbstractField
{

    public Type $type = Type::text;

}