<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class Keyword extends AbstractField
{

    public Type $type = Type::keyword;

    public function normalizer(string $analyzer): void
    {
        $this->options['normalizer'] = $analyzer;
    }

}