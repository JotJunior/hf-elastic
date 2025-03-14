<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class RankFeatureType extends AbstractField
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
