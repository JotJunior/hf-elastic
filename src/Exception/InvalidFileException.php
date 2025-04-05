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

use Exception;
use Throwable;

use function Hyperf\Translation\__;

class InvalidFileException extends Exception
{
    public function __construct(string $fileName = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = __('hf-elastic.invalid_file', ['file' => $fileName]);
        parent::__construct($message, $code, $previous);
    }
}
