<?php

namespace Jot\HfElastic\Exception;

class IndexExistsException extends \Exception
{
    protected $message = 'Index already exists';
}
