<?php

declare(strict_types=1);

namespace Jot\HfElastic\Contracts;

use Elasticsearch\Client;

/**
 * Interface for creating Elasticsearch clients.
 */
interface ClientFactoryInterface
{
    /**
     * Builds and returns an Elasticsearch client.
     *
     * @return Client The configured Elasticsearch client.
     */
    public function build(): Client;
}
