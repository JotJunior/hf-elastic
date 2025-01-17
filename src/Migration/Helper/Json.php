<?php

namespace Jot\HfElastic\Migration\Helper;

use Hyperf\Stringable\Str;

class Json
{

    protected array $json = [];

    protected array $protectedFields = ['id', 'created_at', 'updated_at', '@version', '@timestamp'];

    public function __construct(string $fileName)
    {
        try {
            $this->json = json_decode(file_get_contents($fileName), true);
        } catch (\Throwable $e) {
            throw new \Exception("'$fileName' is not a valid file or url.");
        }

        if (json_last_error_msg() !== 'No error') {
            throw new \Exception("'$fileName' is not valid JSON");
        }

    }


    public function body(string $var = 'index', array $json = [])
    {

        if (empty($json)) {
            $json = $this->json;
        }

        $var = Str::snake($var);

        $migration = '';

        foreach ($json as $field => $value) {

            if (in_array($field, $this->protectedFields)) {
                continue;
            }

            $type = $this->inferElasticType($value);

            switch ($type) {
                case 'object':
                    $migration .= sprintf("\$%s = new ObjectType('%s');\n", $field, $field);
                    $migration .= $this->body($field, $value);
                    $migration .= sprintf("\$%s->child($%s);\n", $var, $field);
                    break;
                case 'nested':
                    $migration .= sprintf("\$%s = new Nested('%s');\n", $field, $field);
                    $migration .= $this->body($field, $this->getProperties($value));
                    $migration .= sprintf("\$%s->nested(\$%s);\n", $var, $field);
                    break;
                default:
                    $migration .= sprintf("\$%s->%s('%s');\n", $var, $type, $field);
                    break;
            }

        }

        return $migration;
    }

    protected function inferElasticType($value): string
    {
        if (is_string($value)) {
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})$/', $value)) {
                return 'date';
            }
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return 'keyword';
            }
            if (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
                return 'ip';
            }
            if (strlen($value) > 200) {
                return 'text';
            }
            return 'keyword';
        }

        if (is_int($value)) {
            return 'long';
        }

        if (is_float($value)) {
            return 'double';
        }

        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_array($value)) {
            if (isset($value[0]) && is_array($value[0])) {
                return 'nested';
            } elseif (isset($value[0]) && !is_array($value[0])) {
                return 'keyword';
            }
            return 'object';
        }

        return 'keyword';
    }

    function getProperties(array $nestedArray): array
    {
        $result = [];

        if (isset($nestedArray[0])) {
            foreach ($nestedArray as $subKey => $subValue) {
                if (is_array($subValue)) {
                    foreach ($subValue as $key => $value) {
                        if (is_array($value)) {
                            $result[$key] = array_merge_recursive($result[$key] ?? [], $this->getProperties($value));
                        } else {
                            $result[$key] = $value;
                        }
                    }
                } else {
                    $result[$subKey] = $subValue;
                }
            }
        } else {
            foreach ($nestedArray as $key => $value) {
                if (is_array($value)) {
                    $result[$key] = array_merge_recursive($result[$key] ?? [], $this->getProperties($value));
                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

}