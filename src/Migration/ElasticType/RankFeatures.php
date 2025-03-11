<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class RankFeatures extends AbstractField
{

    public Type $type = Type::rankFeatures;

    protected array $options = [
        'positive_score_impact' => null
    ];

    public function positiveScoreImpact(bool $value): self
    {
        $this->options['positive_score_impact'] = $value;
        return $this;
    }

}