<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Exception;

use RuntimeException;
use Throwable;

class UnsupportedTypeException extends RuntimeException
{
    protected $message = 'Unsupported field type: %s.';

    public function __construct(string $fieldName = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf($this->message, $fieldName);
        parent::__construct($message, $code, $previous);
    }
}
