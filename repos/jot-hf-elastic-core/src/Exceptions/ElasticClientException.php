<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Exceptions;

/**
 * Exception thrown when there is an error with the Elasticsearch client.
 */
class ElasticClientException extends \RuntimeException
{
    // This class extends RuntimeException and doesn't add any additional functionality.
    // It exists to provide a specific exception type for Elasticsearch client errors.
}
