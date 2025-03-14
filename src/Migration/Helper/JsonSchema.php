<?php

namespace Jot\HfElastic\Migration\Helper;

use Hyperf\Stringable\Str;
use Jot\HfElastic\Contracts\MappingGeneratorInterface;
use Jot\HfElastic\Migration\ElasticType\NestedType;
use Jot\HfElastic\Migration\ElasticType\ObjectType;

class JsonSchema implements MappingGeneratorInterface
{

    protected array $schema;

    protected array $protectedFields = ['updated_at', '@version', '@timestamp'];

    public function __construct(string $fileName)
    {
        if (!file_exists($fileName)) {
            throw new \Exception("'$fileName' is not a valid file or url.");
        }
        
        try {
            $this->schema = json_decode(file_get_contents($fileName), true);
        } catch (\Throwable $e) {
            throw new \Exception("'$fileName' could not be read: " . $e->getMessage());
        }

        if (json_last_error_msg() !== 'No error') {
            throw new \Exception("'$fileName' is not valid JSON");
        }

    }

    /**
     * String representation of the generator output.
     * @return string The generated mapping code.
     */
    public function __toString(): string
    {
        return $this->body();
    }

    public function body(string $var = 'index', array $data = []): string
    {
        if (empty($data)) {
            $schema = $this->schema;
        } else {
            $schema = $data;
        }

        $var = Str::snake($var);

        $migration = '';

        foreach ($schema['properties'] as $field => $definition) {

            $field = Str::snake($field);

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
                $migration .= sprintf("\$%s->object(\$%s);\n", $var, $field);
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
}
