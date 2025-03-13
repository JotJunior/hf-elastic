<?php

declare(strict_types=1);

namespace Jot\HfElasticCore\Types;

use Jot\HfElasticCore\Contracts\ElasticTypeInterface;

/**
 * Nested field type for Elasticsearch.
 * Used for arrays of objects that should be indexed as separate documents.
 */
class NestedType extends ObjectType
{
    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->type = 'nested';
    }

    /**
     * Set the include_in_parent parameter.
     *
     * @param bool $includeInParent
     * @return self
     */
    public function setIncludeInParent(bool $includeInParent): self
    {
        return $this->setProperty('include_in_parent', $includeInParent);
    }

    /**
     * Set the include_in_root parameter.
     *
     * @param bool $includeInRoot
     * @return self
     */
    public function setIncludeInRoot(bool $includeInRoot): self
    {
        return $this->setProperty('include_in_root', $includeInRoot);
    }
}
