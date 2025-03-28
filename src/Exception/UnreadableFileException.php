<?php

namespace Jot\HfElastic\Exception;

use Throwable;
use function Hyperf\Translation\__;

class UnreadableFileException extends \Exception
{
    public function __construct(string $fileName = "", int $code = 0, ?Throwable $previous = null)
    {
        $message = __('messages.hf_elastic.unreadable_file', ['file' => $fileName]);
        parent::__construct($message, $code, $previous);
    }
}
