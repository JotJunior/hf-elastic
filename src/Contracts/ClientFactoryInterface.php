<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Contracts;

use Elasticsearch\Client;

/**
 * Interface for creating Elasticsearch clients.
 */
interface ClientFactoryInterface
{
    /**
     * Builds and returns an Elasticsearch client.
     * @return Client the configured Elasticsearch client
     */
    public function build(): Client;
}
