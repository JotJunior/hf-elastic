<?php

namespace Jot\HfElastic\Migration\Helper;

use Hyperf\Stringable\Str;
use Jot\HfElastic\Contracts\MappingGeneratorInterface;
use Jot\HfElastic\Exception\InvalidFileException;
use Jot\HfElastic\Exception\InvalidJsonTemplateException;
use Jot\HfElastic\Exception\UnreadableFileException;
use JsonException;

class Json implements MappingGeneratorInterface
{

    private const EMPTY_ARRAY = [];

    protected array $json = [];
    protected array $protectedFields = ['updated_at', '@version', '@timestamp'];

    public function __construct(string $fileName)
    {
        if (!file_exists($fileName)) {
            throw new InvalidFileException($fileName);
        }

        try {
            $this->json = json_decode(file_get_contents($fileName), true);
        } catch (\Throwable $e) {
            throw new UnreadableFileException($fileName);
        }

        if (json_last_error_msg() !== 'No error') {
            throw new JsonException("'$fileName' is not valid JSON");
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
            $json = $this->json;
        } else {
            $json = $data;
        }

        $var = Str::snake($var);

        $migration = '';

        foreach ($json as $field => $value) {

            $field = Str::snake($field);

            if (in_array($field, $this->protectedFields)) {
                continue;
            }

            $type = $this->inferElasticType($value);

            switch ($type) {
                case 'object':
                    $migration .= sprintf("\$%s = new ObjectType('%s');\n", $field, $field);
                    $migration .= $this->body($field, $value);
                    $migration .= sprintf("\$%s->object($%s);\n", $var, $field);
                    break;
                case 'nested':
                    $migration .= sprintf("\$%s = new NestedType('%s');\n", $field, $field);
                    $migration .= $this->body($field, $this->getProperties($value));
                    $migration .= sprintf("\$%s->nested(\$%s);\n", $var, $field);
                    break;
                default:
                    $migration .= sprintf("\$%s->addField('%s', '%s');\n", $var, $type, $field);
                    break;
            }

        }

        return $migration;
    }

    protected function inferElasticType($value): string
    {
        if (is_null($value)) {
            throw new InvalidJsonTemplateException();
        }
        return match (true) {
            !is_array($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})$/', $value) => 'date',
            !is_array($value) && filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) => 'ip',
            !is_array($value) && strlen($value) > 200 => 'text',
            !is_array($value) && is_int($value) => 'long',
            !is_array($value) && is_float($value) => 'double',
            !is_array($value) && is_bool($value) => 'boolean',
            is_array($value) && isset($value[0]) && is_array($value[0]) => 'nested',
            is_array($value) && isset($value[0]) && !is_array($value[0]) => 'keyword',
            is_array($value) => 'object',
            default => 'keyword'
        };

    }


    public function getProperties(array $nestedArray): array
    {
        if (empty($nestedArray)) {
            return self::EMPTY_ARRAY;
        }

        $result = [];
        $isIndexedArray = isset($nestedArray[0]);

        foreach ($nestedArray as $key => $value) {
            if (!is_array($value)) {
                $result[$key] = $value;
                continue;
            }

            if ($isIndexedArray) {
                foreach ($value as $subKey => $subValue) {
                    $result[$subKey] = $this->processValue($subKey, $subValue, $result);
                }
            } else {
                $result[$key] = $this->processValue($key, $value, $result);
            }
        }

        return $result;
    }

    private function processValue(string|int $key, mixed $value, array $result): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        return array_merge_recursive(
            $result[$key] ?? [],
            $this->getProperties($value)
        );
    }

}
