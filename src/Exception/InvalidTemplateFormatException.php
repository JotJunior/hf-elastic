<?php

namespace Jot\HfElastic\Exception;

class InvalidTemplateFormatException extends \RuntimeException
{
    protected $message = 'You can only use one of the options --json-schema or --json';
}
