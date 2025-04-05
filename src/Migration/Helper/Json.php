<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Migration\Helper;

use Hyperf\Stringable\Str;
use Jot\HfElastic\Contracts\MappingGeneratorInterface;
use Jot\HfElastic\Exception\InvalidFileException;
use Jot\HfElastic\Exception\InvalidJsonTemplateException;
use Jot\HfElastic\Exception\UnreadableFileException;
use JsonException;
use Throwable;

class Json implements MappingGeneratorInterface
{
    private const EMPTY_ARRAY = [];

    protected array $json = [];

    protected array $protectedFields = ['updated_at', '@version', '@timestamp'];

    public function __construct(string $fileName)
    {
        if (! file_exists($fileName)) {
            throw new InvalidFileException($fileName);
        }

        try {
            $this->json = json_decode(file_get_contents($fileName), true);
        } catch (Throwable $e) {
            throw new UnreadableFileException($fileName);
        }

        if (json_last_error_msg() !== 'No error') {
            throw new JsonException("'{$fileName}' is not valid JSON");
        }
    }

    /**
     * String representation of the generator output.
     * @return string the generated mapping code
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

    public function getProperties(array $nestedArray): array
    {
        if (empty($nestedArray)) {
            return self::EMPTY_ARRAY;
        }

        $result = [];
        $isIndexedArray = isset($nestedArray[0]);

        foreach ($nestedArray as $key => $value) {
            if (! is_array($value)) {
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

    protected function inferElasticType($value): string
    {
        if (is_null($value)) {
            throw new InvalidJsonTemplateException();
        }
        
        // Handle non-array values
        if (!is_array($value)) {
            // Check for date format (only for strings)
            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})$/', $value)) {
                return 'date';
            }
            
            // Check for IP address (only for strings)
            if (is_string($value) && filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
                return 'ip';
            }
            
            // Check for long text (only for strings)
            if (is_string($value) && strlen($value) > 200) {
                return 'text';
            }
            
            // Handle primitive types
            if (is_int($value)) {
                return 'long';
            }
            
            if (is_float($value)) {
                return 'double';
            }
            
            if (is_bool($value)) {
                return 'boolean';
            }
            
            // Default for strings
            return 'keyword';
        }
        
        // Handle array values
        if (isset($value[0])) {
            if (is_array($value[0])) {
                return 'nested';
            }
            return 'keyword'; // Array of primitives
        }
        
        // Associative array
        return 'object';
    }

    private function processValue(int|string $key, mixed $value, array $result): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        return array_merge_recursive(
            $result[$key] ?? [],
            $this->getProperties($value)
        );
    }
}
