<?php

namespace Jot\HfElastic\Migration\ElasticsearchType;

use Jot\HfElastic\Migration\AbstractField;

class RankFeature extends AbstractField
{

    public Type $type = Type::rankFeature;

    protected array $options = [
        'positive_score_impact' => null
    ];

    public function positiveScoreImpact(bool $value): self
    {
        $this->options['positive_score_impact'] = $value;
        return $this;
    }

}