<?php

namespace Jot\HfElastic\Migration\ElasticType;

use Jot\HfElastic\Migration\Property;

class ObjectType extends Property
{

    public Type $type = Type::object;

    protected array $options = [
        'dynamic' => null,
        'enabled' => null,
        'subobjects' => null,
        'properties' => null,
    ];

    public function dynamic(bool $value): self
    {
        $this->options['dynamic'] = $value;
        return $this;
    }

    public function enabled(bool $value): self
    {
        $this->options['enabled'] = $value;
        return $this;
    }

    public function subobjects(bool $value): self
    {
        $this->options['subobjects'] = $value;
        return $this;
    }
    
    /**
     * Retorna as propriedades do objeto
     * 
     * @return array
     */
    public function getProperties(): array
    {
        $properties = [];
        
        foreach ($this->fields as $field) {
            $options = $field->getOptions();
            $type = $this->convertTypeNameToSnakeCase($field->getType()->name);
            
            // Se for um objeto aninhado, adicionar as propriedades
            if (method_exists($field, 'getProperties')) {
                $nestedProperties = $field->getProperties();
                $properties[$field->getName()] = [
                    'type' => $type,
                    'properties' => $nestedProperties
                ];
            } else {
                $properties[$field->getName()] = array_merge(['type' => $type], $options);
            }
        }
        
        return $properties;
    }
    
    /**
     * Converte o nome do enum para snake_case
     * 
     * @param string $typeName Nome do tipo
     * @return string
     */
    private function convertTypeNameToSnakeCase(string $typeName): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $typeName));
    }
}