<?php

namespace Jot\HfElastic\Exception;

use function Hyperf\Translation\__;

class InvalidTemplateFormatException extends \RuntimeException
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        $message = $message ?: __('hf-elastic.invalid_template_format');
        parent::__construct($message, $code, $previous);
    }
}
