<?php

namespace Jot\HfElastic\Exception;

use Throwable;

class UnsupportedTypeException extends \RuntimeException
{
    protected $message = 'Unsupported field type: %s.';

    public function __construct(string $fieldName = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf($this->message, $fieldName);
        parent::__construct($message, $code, $previous);
    }
}
