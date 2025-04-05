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

use function Hyperf\Translation\__;

class InvalidTemplateFormatException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = $message ?: __('hf-elastic.invalid_template_format');
        parent::__construct($message, $code, $previous);
    }
}
