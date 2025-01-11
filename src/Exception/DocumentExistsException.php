<?php

namespace Jot\HfElastic\Exception;

class DocumentExistsException extends \Exception
{
    protected $message = 'Document already exists';
}