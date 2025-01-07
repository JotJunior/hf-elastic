<?php

namespace Jot\HfElastic\Migration;

interface FieldInterface
{

    public function options(array $options): self;
}