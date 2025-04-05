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

class InvalidJsonTemplateException extends RuntimeException
{
    protected $message = 'The json template cannot contains null values.';
}
