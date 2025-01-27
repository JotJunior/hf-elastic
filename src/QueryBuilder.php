<?php

/**
 * Class QueryBuilder
 * Provides a fluent interface to build complex Elasticsearch queries.
 */

namespace Jot\HfElastic;

use Elasticsearch\Client;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Stringable\Str;

use stdClass;

class QueryBuilder
{

    protected string $index;
    protected string $indexPrefix = '';
    protected Client $client;
    protected array $body = [];
    protected array $query = [];
    protected array $aggs = [];
    protected array $ignoredParamsForCount = [
        '_source',
        'sort',
        'size',
        'from',
        'aggs',
        'scroll',
        'terminate_after',
        'track_total_hits',
        'track_scores',
        'version',
        'explain'
    ];

    public function __construct(ContainerInterface $container)
    {
        $this->client = $container->get(ClientBuilder::class)->build();
        $this->indexPrefix = $container->get(ConfigInterface::class)->get('hf_elastic')['prefix'] ?? '';
    }

    /**
     * Specifies the fields to be retrieved from the data source.
     *
     * @param string|array $fields The field or fields to select. Can be a single field name as a string or an array of field names. Defaults to '*'.
     * @return self
     */
    public function select(string|array $fields = '*'): self
    {
        $this->body['_source'] = is_array($fields) ? $fields : [];
        return $this;
    }

    /**
     * Sets the index for the current operation.
     *
     * @param string $index The name of the index to set.
     * @return self
     */
    public function from(string $index): self
    {
        $this->index = $this->index($index);
        return $this;
    }

    /**
     * Specifies the index to be used for the operation.
     *
     * @param string $index The name of the index.
     * @return self Returns the current instance for method chaining.
     */
    public function into(string $index): self
    {
        $this->from($index);
        return $this;
    }

    /**
     * Adds a condition to the query based on the specified field, operator, and value.
     *
     * @param string $field The field to apply the condition to.
     * @param mixed $operator The operator to apply (e.g., '=', '!=', '>', '<', etc.) or a value directly.
     * @param mixed|null $value The value to compare against; optional if the operator is used as a value.
     * @param string $context The context of the condition, such as 'must', 'must_not', or 'should'.
     *
     * @return self
     *
     * @throws \InvalidArgumentException If an unsupported operator is provided.
     */
    public function where(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self
    {
        // Ensure the given context (must, must_not, should) exists within the bool query
        if (!isset($this->query['bool'][$context])) {
            $this->query['bool'][$context] = [];
        }

        // Handle cases where the operator is directly a value (e.g., `where('field', 'value')`)
        if (is_null($value)) {
            $value = $operator;
            $this->query['bool'][$context][] = ['term' => [$field => $value]];
            return $this;
        }

        // Handle cases with specific operators like '=', '!=', '>', '<', '>=', '<=', etc.
        switch ($operator) {
            case '=':
                $this->query['bool'][$context][] = ['term' => [$field => $value]];
                break;

            case '!=':
                if ($context === 'must') {
                    $this->query['bool']['must_not'][] = ['term' => [$field => $value]];
                } else {
                    $this->query['bool'][$context][] = ['bool' => ['must_not' => [['term' => [$field => $value]]]]];
                }
                break;

            case '>':
                $this->query['bool'][$context][] = ['range' => [$field => ['gt' => $value]]];
                break;

            case '<':
                $this->query['bool'][$context][] = ['range' => [$field => ['lt' => $value]]];
                break;

            case '>=':
                $this->query['bool'][$context][] = ['range' => [$field => ['gte' => $value]]];
                break;

            case '<=':
                $this->query['bool'][$context][] = ['range' => [$field => ['lte' => $value]]];
                break;

            case 'exists':
                $this->query['bool'][$context][] = ['exists' => ['field' => $value]];
                break;

            case 'distance':
                $this->query['bool'][$context][] = ['geo_distance' => ['distance' => $value['distance'], $field => ['lat' => $value['lat'], 'lon' => $value['lon']]]];
                break;

            case 'between':
                $this->query['bool'][$context][] = ['range' => [$field => ['gte' => $value[0], 'lte' => $value[1]]]];
                break;

            case 'like':
                $this->query['bool'][$context][] = ['wildcard' => [$field => str_replace('%', '*', $value)]];
                break;

            case 'prefix':
                $this->query['bool'][$context][] = ['prefix' => [$field => $value]];
                break;

            default:
                throw new \InvalidArgumentException("Unsupported operator: {$operator}");
        }

        return $this;
    }

    /**
     * Adds an additional condition to the query using a logical "AND" operator.
     *
     * @param string $field The field to apply the condition on.
     * @param mixed $operator The operator to use for the condition.
     * @param mixed|null $value The value to compare the field against. Optional.
     * @param string $context The context in which the condition will be applied (default is 'must').
     *
     * @return self
     */
    public function andWhere(string $field, mixed $operator, mixed $value = null, string $context = 'must'): self
    {
        return $this->where($field, $operator, $value, $context);
    }

    /**
     * Adds a condition to the query with a logical "OR" relationship. Supports various operators
     * including equality, inequality, range, existence, geospatial distance, and more.
     *
     * @param string $field The name of the field to which the condition applies.
     * @param mixed $operator The operator or value for the condition. Supported operators include
     *                        '=', '!=', '>', '<', '>=', '<=', 'exists', 'distance', 'between', 'like', etc.
     *                        If the $value parameter is omitted, this parameter represents the condition's value.
     * @param mixed|null $value The value to be used with the operator. If omitted, the $operator is assumed to be the value.
     * @param string $subContext The sub-context within the query, defaulting to "should".
     *
     * @return self Returns the current instance for method chaining.
     */
    public function orWhere(string $field, mixed $operator, mixed $value = null, string $subContext = 'should'): self
    {
        // Ensure the top-level bool query and the should context exist
        if (!isset($this->query['bool'][$subContext])) {
            $this->query['bool'][$subContext] = [];
        }

        // Handle cases where the operator is directly a value (e.g., `orWhere('field', 'value')`)
        if (is_null($value)) {
            $value = $operator;
            $this->query['bool'][$subContext][] = ['term' => [$field => $value]];
            return $this;
        }

        // Handle cases with specific operators like '=', '!=', '>', '<', '>=', '<=', etc.
        switch ($operator) {
            case '=':
                $this->query['bool'][$subContext][] = ['term' => [$field => $value]];
                break;

            case '!=':
                $this->query['bool'][$subContext][] = ['bool' => ['must_not' => [['term' => [$field => $value]]]]];
                break;

            case '>':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['gt' => $value]]];
                break;

            case '<':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['lt' => $value]]];
                break;

            case '>=':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['gte' => $value]]];
                break;

            case '<=':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['lte' => $value]]];
                break;

            case 'exists':
                $this->query['bool'][$subContext][] = ['exists' => ['field' => $value]];
                break;

            case 'distance':
                $this->query['bool'][$subContext][] = ['geo_distance' => ['distance' => $value['distance'], $field => ['lat' => $value['lat'], 'lon' => $value['lon']]]];
                break;

            case 'between':
                $this->query['bool'][$subContext][] = ['range' => [$field => ['gte' => $value[0], 'lte' => $value[1]]]];
                break;

            case 'like':
                $this->query['bool'][$subContext][] = ['wildcard' => [$field => str_replace('%', '*', $value)]];
                break;

            case 'prefix':
                $this->query['bool'][$subContext][] = ['prefix' => [$field => $value]];
                break;

            default:
                throw new \InvalidArgumentException("Unsupported operator: {$operator}");
        }

        return $this;
    }

    /**
     * Adds a "must" condition to the query by applying the provided callback to a subquery instance.
     *
     * @param callable $callback A callback function that defines the conditions for the "must" clause.
     * @return self The current instance with the updated "must" condition.
     */
    public function whereMust(callable $callback): self
    {
        $subQuery = new self();
        $callback($subQuery);
        $this->query['bool']['must'][] = ['bool' => $subQuery->query['bool']];
        return $this;
    }

    /**
     * Adds a "should" condition to the query by applying the provided callback to a new subquery instance.
     *
     * @param callable $callback A callback function that defines the criteria for the "should" condition by modifying the subquery.
     * @return self
     */
    public function whereShould(callable $callback): self
    {
        $subQuery = new self();
        $callback($subQuery);
        $this->query['bool']['must'][] = ['bool' => ['should' => $subQuery->query['bool']['must']]];
        return $this;
    }

    /**
     * Sets the limit for the number of results to retrieve.
     *
     * @param int $limit The maximum number of results to retrieve.
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->body['size'] = $limit;
        return $this;
    }

    /**
     * Sets the starting point for a query or operation using the provided offset value.
     *
     * @param int $offset The starting offset for the operation.
     * @return self Returns the current instance for method chaining.
     */
    public function offset(int $offset): self
    {
        $this->body['from'] = $offset;
        return $this;
    }

    /**
     * Adds a nested query to the current query, applying a given callback to define the nested query's structure.
     *
     * @param string $path The path to the nested object in the query.
     * @param callable $callback A callback that defines the nested query logic by receiving a sub-query instance.
     * @return self Returns the current instance for method chaining.
     */
    public function whereNested(string $path, callable $callback): self
    {
        $subQuery = new self();
        $callback($subQuery);
        $this->query['bool']['must'][] = ['nested' => ['path' => $path, 'query' => $subQuery->query]];
        return $this;
    }

    /**
     * Specifies the sorting criteria for the query by field and order.
     *
     * @param string $field The name of the field to sort by.
     * @param string $order The sorting order, either 'asc' for ascending or 'desc' for descending. Defaults to 'asc'.
     * @return self Returns the current instance for method chaining.
     */
    public function orderBy(string $field, string $order = 'asc'): self
    {
        $this->body['sort'][] = [$field => $order];
        return $this;
    }

    /**
     * Adds a geo-distance query to filter results based on the specified field, location, and distance.
     *
     * @param string $field The name of the field to apply the geo-distance filter.
     * @param string $location The geographic location specified as a coordinate (e.g., "lat,lon").
     * @param string $distance The maximum distance from the location within which results should be included.
     * @return self Returns the current instance for method chaining.
     */
    public function geoDistance(string $field, string $location, string $distance): self
    {
        $this->query['bool']['must'][] = ['geo_distance' => ['distance' => $distance, 'location' => $location]];
        return $this;
    }

    /**
     * Converts the current query data into an array format suitable for execution or further processing.
     *
     * @return array Returns an associative array representation of the query, including index, body, and optional aggregations.
     */
    public function toArray(): array
    {
        $this->query['bool']['filter'][] = ['term' => ['deleted' => false]];
        $query = [
            'index' => $this->index,
            'body' => [
                ...$this->body,
                'query' => $this->query
            ]
        ];

        if (!empty($this->aggs)) {
            $query['body']['aggs'] = $this->aggs;
        }

        return $query;
    }


    /**
     * Executes a search query on the specified index and retrieves matching results.
     *
     * @return array Returns an array of search hits retrieved from the query execution.
     */
    public function execute(): array
    {
        $query = $this->toArray();

        try {
            $result = $this->client->search([
                'index' => $query['index'],
                'body' => $query['body'],
            ]);
            $this->reset();
            return [
                'data' => array_map(fn($hit) => $hit['_source'], $result['hits']['hits']),
                'result' => 'success',
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => $this->parseError($e),
            ];
        }
    }

    /**
     * Counts the number of documents in the index matching the specified query.
     *
     * This method sends a count query to Elasticsearch and retrieves the number
     * of documents that match the query parameters provided in the request body.
     *
     * @return int Returns the total number of documents that satisfy the query criteria.
     * @throws \InvalidArgumentException If the query parameters are invalid or not properly defined.
     */
    public function count(): int
    {
        $query = $this->toArray();
        foreach ($this->ignoredParamsForCount as $ignoredParam) {
            unset($query['body'][$ignoredParam]);
        }
        $result = $this->client->count([
            'index' => $query['index'],
            'body' => $query['body'],
        ]);
        $this->reset();
        return $result['count'];
    }


    /**
     * Inserts a new document into the specified index. If the document already exists, an exception is thrown.
     *
     * @param array $data The data to be inserted, including an optional 'id' key for the document ID.
     * @return array An associative array containing the result status, any errors encountered, and the inserted data.
     */
    public function insert(array $data): array
    {
        if (!empty($data['id']) && $this->getDocumentVersion($data['id'])) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => sprintf('Document with id %s already exists.', $data['id']),
            ];
        }

        $data = [
            ...$data,
            'created_at' => (new \DateTime('now'))->format(DATE_ATOM),
            'updated_at' => (new \DateTime('now'))->format(DATE_ATOM),
            'deleted' => false,
            '@timestamp' => \DateTime::createFromFormat('U.u', microtime(true))->format('Y-m-d\TH:i:s.u\Z'),
            '@version' => 1,
        ];

        try {
            $data['id'] = $data['id'] ?? Str::uuid()->toString();
            $result = $this->client->create([
                'index' => $this->index,
                'id' => $data['id'],
                'body' => $data,
            ]);
        } catch (\Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => $this->parseError($e)
            ];
        }

        return [
            'data' => $data,
            'result' => $result['result'],
            'error' => null,
        ];

    }


    /**
     * Updates a document by its identifier with the provided data.
     *
     * @param string $id The unique identifier of the document to update.
     * @param array $data The data to update the document with, excluding internal fields such as '@timestamp'.
     * @return array Returns an array containing the result of the operation, potential error message, and the updated data.
     */
    public function update(string $id, array $data): array
    {

        $currentVersion = $this->getDocumentVersion($id);

        if (empty($currentVersion)) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => 'Document not found',
            ];
        }

        unset($data['@timestamp']);
        unset($data['@version']);
        $data['@version'] = ++$currentVersion;
        $data['updated_at'] = (new \DateTime('now'))->format(DATE_ATOM);

        try {
            $result = $this->client->update([
                'index' => $this->index,
                'id' => $id,
                'body' => [
                    'doc' => $data,
                ],
            ]);
        } catch (\Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => $this->parseError($e),
            ];
        }

        return [
            'data' => $data,
            'result' => $result['result'],
            'error' => null,
        ];
    }


    /**
     * Retrieves the version number of a document based on its identifier.
     *
     * @param string $id The unique identifier of the document.
     * @return int|null Returns the version number of the document if found, or null if the document does not exist or has no version available.
     */
    protected function getDocumentVersion(string $id): ?int
    {
        $completeData = $this->select(['@version'])
            ->from($this->index)
            ->where('id', '=', $id)
            ->where('deleted', '=', false)
            ->execute();

        return $completeData['data'][0]['@version'] ?? null;
    }


    /**
     * Deletes a document either logically or physically based on the given parameters.
     *
     * @param string $id The unique identifier of the document to delete.
     * @param bool $logicalDeletion Determines if the deletion should be logical (true) or physical (false). Defaults to true.
     *
     * @return array Returns an array containing the result of the operation, any error information, and additional data.
     */
    public function delete(string $id, bool $logicalDeletion = true): array
    {

        $currentVersion = $this->getDocumentVersion($id);

        if ($logicalDeletion) {
            if (empty($currentVersion)) {
                return [
                    'result' => 'error',
                    'error' => 'Document not found',
                    'data' => []
                ];
            }
            $data = ['deleted' => true];
            return $this->update($id, $data);
        }

        try {
            $result = $this->client->delete([
                'index' => $this->index,
                'id' => $id,
            ]);
            return [
                'data' => null,
                'result' => $result['result'],
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'data' => null,
                'result' => 'error',
                'error' => $this->parseError($e),
            ];
        }

    }


    /**
     * Performs a bulk update operation on documents matching the specified query.
     *
     * @param array $data The data to update within the matched documents.
     *                    The provided data will be merged with the existing document data.
     * @return array Returns an associative array containing the count of updated documents and their IDs:
     *               - 'updated_count' (int): The total number of documents updated.
     *               - 'updated_ids' (array): The list of updated document IDs.
     * @throws \InvalidArgumentException If no query parameter is specified or the query is invalid.
     */
    public function bulkUpdate(array $data): array
    {
        $query = $this->toArray();
        if (empty($query['body']['query']) || $query['body']['query'] = ['match_all' => new stdClass()]) {
            throw new \InvalidArgumentException("At least one query parameter is required for bulk update.");
        }

        $updatedDocuments = [];
        $scrollTimeout = '1m';

        $result = $this->client->search([
            'index' => $this->index,
            'body' => $query['body'],
            'scroll' => $scrollTimeout,
            'size' => 100,
        ]);

        $scrollId = $result['_scroll_id'];
        $hits = $result['hits']['hits'];

        while (!empty($hits)) {
            $bulkBody = [];

            foreach ($hits as $hit) {
                $docId = $hit['_id'];
                $source = $hit['_source'];

                unset($data['@timestamp']);

                $updatedData = array_replace_recursive($source, $data);
                $updatedData['updated_at'] = (new \DateTime('now'))->format(DATE_ATOM);
                $updatedData['@version'] = (int)$source['@version'] + 1;

                $bulkBody[] = [
                    'update' => [
                        '_index' => $this->index,
                        '_id' => $docId,
                    ],
                ];
                $bulkBody[] = ['doc' => $updatedData];

                $updatedDocuments[] = $docId;
            }

            $this->client->bulk(['body' => $bulkBody]);

            $scrollResult = $this->client->scroll([
                'scroll_id' => $scrollId,
                'scroll' => $scrollTimeout,
            ]);

            $scrollId = $scrollResult['_scroll_id'];
            $hits = $scrollResult['hits']['hits'];
        }

        $this->client->clearScroll(['scroll_id' => $scrollId]);

        $this->reset();
        return [
            'updated_count' => count($updatedDocuments),
            'updated_ids' => $updatedDocuments,
        ];
    }


    /**
     * Performs a bulk delete operation on documents matching the specified query.
     * Depending on the value of the logicalDeletion parameter, it either logically deletes documents by updating them
     * or physically removes them from the index.
     *
     * @param bool $logicalDeletion Determines whether the documents are logically deleted (true) or physically deleted (false). Defaults to true.
     * @return array Returns an associative array containing the count of deleted documents and their IDs. Keys:
     *               - 'deleted_count' (int): The number of affected documents.
     *               - 'deleted_ids' (array): A list of IDs of the deleted documents.
     * @throws \InvalidArgumentException Thrown when no query parameter is provided or the query is invalid.
     */
    public function bulkDelete(bool $logicalDeletion = true): array
    {
        $query = $this->toArray();
        if (empty($query['body']['query']) || $query['body']['query'] = ['match_all' => new stdClass()]) {
            throw new \InvalidArgumentException("At least one query parameter is required for bulk update.");
        }

        $updatedDocuments = [];
        $scrollTimeout = '1m';

        $result = $this->client->search([
            'index' => $this->index,
            'body' => $query['body'],
            'scroll' => $scrollTimeout,
            'size' => 100,
        ]);

        $scrollId = $result['_scroll_id'];
        $hits = $result['hits']['hits'];

        while (!empty($hits)) {
            $bulkBody = [];

            foreach ($hits as $hit) {
                $docId = $hit['_id'];
                $updatedData['deleted'] = true;
                $updatedData['updated_at'] = (new \DateTime('now'))->format(DATE_ATOM);
                $updatedData['@version'] = (int)$hit['_source']['@version'] + 1;
                $bulkBody[] = [
                    $logicalDeletion ? 'update' : 'delete' => [
                        '_index' => $this->index,
                        '_id' => $docId,
                    ],
                ];
                if ($logicalDeletion) {
                    $bulkBody[] = ['doc' => $updatedData];
                }

                $updatedDocuments[] = $docId;
            }

            $this->client->bulk(['body' => $bulkBody]);

            $scrollResult = $this->client->scroll([
                'scroll_id' => $scrollId,
                'scroll' => $scrollTimeout,
            ]);

            $scrollId = $scrollResult['_scroll_id'];
            $hits = $scrollResult['hits']['hits'];
        }

        $this->client->clearScroll(['scroll_id' => $scrollId]);

        $this->reset();
        return [
            'deleted_count' => count($updatedDocuments),
            'deleted_ids' => $updatedDocuments,
        ];
    }

    /**
     * Resets the internal state of the object by clearing query, body, and aggs properties.
     *
     * @return void
     */
    private function reset(): void
    {
        $this->query = [];
        $this->body = [];
        $this->aggs = [];
    }

    /**
     * Parses the provided exception to extract and return a meaningful error message.
     * If the exception message contains a valid JSON structure with specific error details,
     * the method retrieves the reason from it; otherwise, a default message is returned.
     *
     * @param \Throwable $exception The exception object containing the error message to parse.
     *
     * @return string The parsed error message or a default message if parsing fails.
     */
    public function parseError(\Throwable $exception)
    {
        $errorDetails = json_decode($exception->getMessage(), true);
        $message = 'Invalid query parameters.';

        if (json_last_error() === JSON_ERROR_NONE && isset($errorDetails['error']['reason'])) {
            $message = $errorDetails['error']['root_cause'][0]['reason'] ?? $errorDetails['error']['reason'];
        }
        return $message;
    }

    /**
     * Generates the full index name by appending a prefix if it is set.
     *
     * @param string $indexName The base name of the index.
     * @return string The full index name, including the prefix if applicable.
     */
    protected function index(string $indexName)
    {
        return $this->indexPrefix ? sprintf('%s_%s', $this->indexPrefix, $indexName) : $indexName;
    }

}