<?php

declare(strict_types=1);

/**
 * Class QueryBuilder
 * Provides a fluent interface to build complex Elasticsearch queries.
 *
 * This class extends the new ElasticQueryBuilder implementation for backward compatibility.
 */

namespace Jot\HfElastic;

use Jot\HfElastic\Query\ElasticQueryBuilder;

class QueryBuilder extends ElasticQueryBuilder
{

}
