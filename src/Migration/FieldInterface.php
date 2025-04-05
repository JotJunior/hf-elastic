<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Migration;

use Jot\HfElastic\Migration\ElasticType\Type;

interface FieldInterface
{
    /**
     * Sets options for the field.
     * @param array $options the options to set
     * @return self returns the instance of the current class
     */
    public function options(array $options): self;

    /**
     * Gets the name of the field.
     * @return string the name of the field
     */
    public function getName(): string;

    /**
     * Gets the options of the field.
     * @return array the options of the field
     */
    public function getOptions(): array;

    /**
     * Gets the type of the field.
     * @return Type the type of the field
     */
    public function getType(): Type;
}
