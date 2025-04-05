<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Contracts;

use Elasticsearch\Client;

/**
 * Interface for Elasticsearch client factory.
 */
interface ClientFactoryInterface
{
    /**
     * Create a new Elasticsearch client instance.
     *
     * @return Client
     */
    public function createClient(): Client;
}
