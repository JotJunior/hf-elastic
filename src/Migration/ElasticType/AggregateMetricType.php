<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\AbstractField;

class AggregateMetricType extends AbstractField
{

    public Type $type = Type::aggregateMetric;

    protected array $options = [
        'type' => 'aggregate_metric_double',
        'metrics' => ['min', 'max', 'avg', 'sum', 'value_count'],
        'default_metric' => 'max'
    ];

    public function type(string $value = 'aggregate_metric_double'): self
    {
        $this->options['type'] = $value;
        return $this;
    }

    public function metrics(array $value = ['min', 'max', 'avg', 'sum', 'value_count']): self
    {
        $this->options['metrics'] = $value;
        return $this;
    }

    public function defaultMetric(string $value = 'max'): self
    {
        $this->options['default_metric'] = $value;
        return $this;
    }

}
