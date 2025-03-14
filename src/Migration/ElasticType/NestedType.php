<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\Property;

class NestedType extends Property
{

    public Type $type = Type::nested;

    protected array $options = [
        'dynamic' => null,
        'properties' => null,
        'include_in_parent' => null,
        'include_in_root' => null,
    ];

    public function dynamic(bool $value): self
    {
        $this->options['dynamic'] = $value;
        return $this;
    }

    public function properties(array $properties): self
    {
        $this->options['properties'] = $properties;
        return $this;
    }

    public function includeInParent(bool $value): self
    {
        $this->options['include_in_parent'] = $value;
        return $this;
    }

    public function includeInRoot(bool $value): self
    {
        $this->options['include_in_root'] = $value;
        return $this;
    }
    
    /**
     * Retorna as propriedades do objeto aninhado
     * @return array
     */
    public function getProperties(): array
    {
        $properties = [];
        
        foreach ($this->fields as $field) {
            $options = $field->getOptions();
            $type = $this->convertTypeNameToSnakeCase($field->getType()->name);
            $properties[$field->getName()] = array_merge(['type' => $type], $options);
        }
        
        return $properties;
    }
    
    /**
     * Converte o nome do enum para snake_case
     * @param string $typeName Nome do tipo
     * @return string
     */
    private function convertTypeNameToSnakeCase(string $typeName): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $typeName));
    }

}
