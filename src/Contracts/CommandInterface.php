<?php

declare(strict_types=1);

namespace Jot\HfElastic\Contracts;

interface CommandInterface
{
    /**
     * Execute the command.
     *
     * @return mixed
     */
    public function handle();
}
