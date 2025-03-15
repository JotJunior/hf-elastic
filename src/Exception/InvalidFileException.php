<?php

namespace Jot\HfElastic\Exception;

use Throwable;

class InvalidFileException extends \Exception
{
    protected $message = '%s is not a valid file or url.';

    public function __construct(string $fileName = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf($this->message, $fileName);
        parent::__construct($message, $code, $previous);
    }
}
