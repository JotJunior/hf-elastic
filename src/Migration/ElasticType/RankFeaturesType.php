<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class RankFeaturesType extends AbstractField
{
    public Type $type = Type::rankFeatures;

    protected array $options = [
        'positive_score_impact' => null,
    ];

    public function positiveScoreImpact(bool $value): self
    {
        $this->options['positive_score_impact'] = $value;
        return $this;
    }
}
