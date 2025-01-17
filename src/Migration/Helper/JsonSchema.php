<?php

namespace Jot\HfElastic\Migration\Helper;

use Hyperf\Stringable\Str;

class JsonSchema
{

    protected array $schema;

    protected array $protectedFields = ['id', 'created_at', 'updated_at', '@version', '@timestamp'];

    public function __construct(string $fileName)
    {
        try {
            $this->schema = json_decode(file_get_contents($fileName), true);
        } catch (\Throwable $e) {
            throw new \Exception("'$fileName' is not a valid file or url.");
        }

        if (json_last_error_msg() !== 'No error') {
            throw new \Exception("'$fileName' is not valid JSON");
        }

    }

    public function body(string $var = 'index', array $schema = []): string
    {
        if (empty($schema)) {
            $schema = $this->schema;
        }

        $var = Str::snake($var);

        $migration = '';

        foreach ($schema['properties'] as $field => $definition) {

            if (in_array($field, $this->protectedFields)) {
                continue;
            }

            $type = $definition['type'] ?? 'text';

            if ($type === 'array' && !isset($definition['items']['properties'])) {
                $type = $definition['items']['type'] ?? 'text';
            }

            $esType = match ($type) {
                'integer' => 'integer',
                'number' => 'float',
                'boolean' => 'boolean',
                'object' => 'object',
                'array' => 'nested',
                default => 'keyword',
            };

            if (!empty($definition['format'])) {
                $esType = match ($definition['format']) {
                    'date-time' => 'date',
                    default => $esType,
                };
            }

            if ($type === 'object' && isset($definition['properties'])) {
                $migration .= sprintf("\$%s = new ObjectType('%s');\n", $field, $field);
                $migration .= $this->body($field, $definition);
                $migration .= sprintf("\$%s->object($%s);\n", $var, $field);
            } elseif ($type === 'array' && isset($definition['items']['properties'])) {
                $migration .= sprintf("\$%s = new NestedType('%s');\n", $field, $field);
                $migration .= $this->body($field, $definition['items']);
                $migration .= sprintf("\$%s->nested(\$%s);\n", $var, $field);
            } else {
                $migration .= sprintf("\$%s->%s('%s');\n", $var, $esType, $field);
            }
        }
        return $migration;

    }

    public function __toString()
    {
        return $this->body();
    }

}