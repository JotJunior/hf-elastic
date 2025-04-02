<?php

namespace Jot\HfElastic\Migration\Helper;

use Hyperf\Stringable\Str;
use Jot\HfElastic\Contracts\MappingGeneratorInterface;
use Jot\HfElastic\Exception\InvalidFileException;
use Jot\HfElastic\Exception\UnreadableFileException;
use JsonException;
use function Hyperf\Translation\__;

class JsonSchema implements MappingGeneratorInterface
{

    protected array $schema;

    protected array $protectedFields = ['updated_at', '@version', '@timestamp'];

    /**
     * @throws InvalidFileException
     * @throws JsonException
     * @throws UnreadableFileException
     */
    public function __construct(string $fileName)
    {
        if (!file_exists($fileName)) {
            throw new InvalidFileException($fileName);
        }

        try {
            $this->schema = json_decode(file_get_contents($fileName), true);
        } catch (\Throwable $e) {
            throw new UnreadableFileException($fileName, 500, $e);
        }

        if (json_last_error_msg() !== 'No error') {
            throw new JsonException(__('hf-elastic.invalid_field', ['field' => "$fileName (JSON)"])); 
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
        $schema = empty($data) ? $this->schema : $data;

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
                'integer' => 'long',
                'number' => 'double',
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
                $migration .= sprintf("\$%s->addField('%s', '%s');\n", $var, $esType, $field);
            }
        }
        return $migration;

    }
}
