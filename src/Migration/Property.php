<?php

namespace Jot\HfElastic\Migration;

use Jot\HfElastic\Contracts\PropertyInterface;
use Jot\HfElastic\Factories\FieldTypeFactory;
use Jot\HfElastic\Migration\ElasticType as T;
use Jot\HfElastic\Migration\ElasticType\Type;

class Property implements PropertyInterface
{
    protected Type $type = Type::object;
    protected FieldInterface $field;
    protected array $fields = [];
    protected array $options = [];
    private FieldTypeFactory $typeFactory;

    public function __construct(protected string $name)
    {
        $this->name = $name;
        $this->typeFactory = new FieldTypeFactory();
    }

    public function object(T\ObjectType $object): T\ObjectType
    {
        return $this->fields[] = $object;
    }

    public function nested(T\NestedType $nested): T\NestedType
    {
        return $this->fields[] = $nested;
    }

    public function getChildren(): array
    {
        return $this->fields;
    }

    public function getOptions(): array
    {
        return array_filter($this->options);
    }

    public function getType(): T\Type
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function keyword(string $name): FieldInterface
    {
        return $this->addField('keyword', $name);
    }

    public function addField(string $type, string $name, ?array $params = null): FieldInterface|PropertyInterface
    {
        return $this->fields[] = $this->typeFactory->create($type, $name, $params);
    }

    public function alias(string $name): FieldInterface
    {
        return $this->addField('alias', $name);
    }

    public function text(string $name): FieldInterface
    {
        return $this->addField('text', $name);
    }

    public function long(string $name): FieldInterface
    {
        return $this->addField('long', $name);
    }

    public function boolean(string $name): FieldInterface
    {
        return $this->addField('boolean', $name);
    }

    public function date(string $name): FieldInterface
    {
        return $this->addField('date', $name);
    }

    public function dateNanos(string $name): FieldInterface
    {
        return $this->addField('date_nanos', $name);
    }


    /**
     * Defines the default fields for the entity, including standard timestamps,
     * version information, and deletion status.
     * @return void
     */
    public function defaults(): void
    {
        $this->fields[] = new T\DateType('created_at');
        $this->fields[] = new T\DateType('updated_at');
        $this->fields[] = new T\BooleanType('deleted');
        $this->fields[] = new T\LongType('@version');
        $this->fields[] = new T\DateNanosType('@timestamp');
    }

}
