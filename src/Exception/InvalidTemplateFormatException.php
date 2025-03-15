<?php

namespace Jot\HfElastic\Exception;

class InvalidJsonTemplateException extends \RuntimeException
{
    protected $message = 'The json template cannot contains null values.';
}
