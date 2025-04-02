<?php

namespace Jot\HfElastic\Exception;

use Throwable;
use function Hyperf\Translation\__;

class IndexExistsException extends \Exception
{
    public function __construct(string $indexName = "", int $code = 0, ?Throwable $previous = null)
    {
        $final = __('hf-elastic.index_already_exists', ['index' => $indexName]);
        parent::__construct($final, $code, $previous);
    }
}
