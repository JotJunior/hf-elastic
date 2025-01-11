<?php

namespace Jot\HfElastic\Exception;

class MissingMigrationDirectoryException extends \Exception
{
    protected $message = 'Missing migration directory';
}