<?php

namespace Jot\HfElastic\Exception;

use Throwable;

class UnreadableFileException extends \Exception
{
    protected $message = '%s could not be read.';

    public function __construct(string $fileName = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf($this->message, $fileName);
        parent::__construct($message, $code, $previous);
    }
}
