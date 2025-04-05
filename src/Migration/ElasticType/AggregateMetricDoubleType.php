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

class AggregateMetricDoubleType extends AbstractField
{
    public Type $type = Type::aggregateMetricDouble;

    protected array $options = [
        'metrics' => null,
        'default_metric' => null,
    ];

    /**
     * Define as métricas para o tipo aggregate_metric_double.
     * @param array $metrics Lista de métricas
     */
    public function metrics(array $metrics): self
    {
        $this->options['metrics'] = $metrics;
        return $this;
    }

    /**
     * Define a métrica padrão para o tipo aggregate_metric_double.
     * @param string $metric Métrica padrão
     */
    public function defaultMetric(string $metric): self
    {
        $this->options['default_metric'] = $metric;
        return $this;
    }
}
