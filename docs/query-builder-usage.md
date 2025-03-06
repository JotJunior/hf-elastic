# ElasticQueryBuilder Usage Guide

The `ElasticQueryBuilder` class provides a fluent interface for building Elasticsearch queries. This guide demonstrates how to use the various methods and features of the builder to construct complex queries and perform CRUD operations.

## Table of Contents

- [Basic Usage](#basic-usage)
- [Query Operations](#query-operations)
  - [Basic Queries](#basic-queries)
  - [Advanced Queries](#advanced-queries)
  - [Nested Queries](#nested-queries)
  - [Geo Queries](#geo-queries)
- [CRUD Operations](#crud-operations)
  - [Creating Records](#creating-records)
  - [Updating Records](#updating-records)
  - [Deleting Records](#deleting-records)
  - [Batch Operations](#batch-operations)

## Basic Usage

To start using the `ElasticQueryBuilder`, you need to create an instance of it. The builder requires several dependencies:

```php
use Jot\HfElastic\Query\ElasticQueryBuilder;

// Assuming you have the required dependencies injected or instantiated
$queryBuilder = new ElasticQueryBuilder(
    $client,            // Elasticsearch\Client
    $indexFormatter,    // IndexNameFormatter
    $operatorRegistry,  // OperatorRegistry
    $queryContext       // QueryContext
);
```

## Query Operations

### Basic Queries

#### Simple Equality Query

```php
$results = $queryBuilder
    ->from('users')
    ->where('name', '=', 'John Doe')
    ->execute();
```

#### Multiple Conditions with AND

```php
$results = $queryBuilder
    ->from('users')
    ->where('age', '>=', 18)
    ->andWhere('status', '=', 'active')
    ->execute();
```

#### Multiple Conditions with OR

```php
$results = $queryBuilder
    ->from('users')
    ->where('role', '=', 'admin')
    ->orWhere('role', '=', 'manager')
    ->execute();
```

#### Limiting Results

```php
$results = $queryBuilder
    ->from('users')
    ->where('status', '=', 'active')
    ->limit(10)
    ->offset(20) // Skip the first 20 results
    ->execute();
```

#### Ordering Results

```php
$results = $queryBuilder
    ->from('users')
    ->orderBy('created_at', 'desc')
    ->execute();
```

#### Selecting Specific Fields

```php
$results = $queryBuilder
    ->from('users')
    ->select(['id', 'name', 'email'])
    ->execute();
```

#### Counting Results

```php
$count = $queryBuilder
    ->from('users')
    ->where('status', '=', 'active')
    ->count();
```

### Advanced Queries

#### Range Queries

```php
// Greater than
$results = $queryBuilder
    ->from('products')
    ->where('price', '>', 100)
    ->execute();

// Less than
$results = $queryBuilder
    ->from('products')
    ->where('price', '<', 50)
    ->execute();

// Between range
$results = $queryBuilder
    ->from('products')
    ->where('price', 'between', [50, 100])
    ->execute();
```

#### Complex Conditions with Must Clauses

```php
$results = $queryBuilder
    ->from('users')
    ->whereMust(function($query) {
        $query->where('status', '=', 'active')
              ->andWhere('email_verified', '=', true);
    })
    ->execute();
```

#### Complex Conditions with Should Clauses

```php
$results = $queryBuilder
    ->from('users')
    ->whereShould(function($query) {
        $query->where('role', '=', 'admin')
              ->orWhere('role', '=', 'manager');
    })
    ->execute();
```

#### Combining Multiple Complex Conditions

```php
$results = $queryBuilder
    ->from('products')
    ->whereMust(function($query) {
        $query->where('status', '=', 'in_stock')
              ->andWhere('price', '<', 1000);
    })
    ->whereShould(function($query) {
        $query->where('category', '=', 'electronics')
              ->orWhere('category', '=', 'computers');
    })
    ->execute();
```

### Nested Queries

For documents with nested objects, you can use the `whereNested` method:

```php
$results = $queryBuilder
    ->from('orders')
    ->whereNested('items', function($query) {
        $query->where('product_id', '=', 123)
              ->andWhere('quantity', '>', 1);
    })
    ->execute();
```

### Geo Queries

For geo-spatial queries, you can use the `geoDistance` method:

```php
$results = $queryBuilder
    ->from('locations')
    ->geoDistance('position', '40.73, -74.1', '10km')
    ->execute();
```

## CRUD Operations

### Creating Records

To create a new document in Elasticsearch:

```php
use Elasticsearch\Client;

// Assuming you have an Elasticsearch client
$client = /* your Elasticsearch client */;

$document = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'created_at' => date('Y-m-d H:i:s')
];

$result = $client->index([
    'index' => 'users',
    'body' => $document
]);

// Check if the document was created successfully
if (isset($result['result']) && $result['result'] === 'created') {
    echo 'Document created with ID: ' . $result['_id'];
}
```

### Updating Records

To update an existing document:

```php
// First, find the document to update
$searchResult = $queryBuilder
    ->from('users')
    ->where('email', '=', 'john@example.com')
    ->execute();

if (!empty($searchResult['data'])) {
    $document = $searchResult['data'][0];
    $documentId = $document['id']; // Assuming the document has an 'id' field
    
    // Update the document
    $result = $client->update([
        'index' => 'users',
        'id' => $documentId,
        'body' => [
            'doc' => [
                'name' => 'John Updated',
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]
    ]);
    
    // Check if the update was successful
    if (isset($result['result']) && $result['result'] === 'updated') {
        echo 'Document updated successfully';
    }
}
```

### Deleting Records

To delete a document:

```php
// First, find the document to delete
$searchResult = $queryBuilder
    ->from('users')
    ->where('email', '=', 'john@example.com')
    ->execute();

if (!empty($searchResult['data'])) {
    $document = $searchResult['data'][0];
    $documentId = $document['id']; // Assuming the document has an 'id' field
    
    // Delete the document
    $result = $client->delete([
        'index' => 'users',
        'id' => $documentId
    ]);
    
    // Check if the deletion was successful
    if (isset($result['result']) && $result['result'] === 'deleted') {
        echo 'Document deleted successfully';
    }
}
```

### Batch Operations

For bulk operations, you can use the Elasticsearch bulk API:

#### Bulk Insert

```php
$params = ['body' => []];

$users = [
    ['name' => 'User 1', 'email' => 'user1@example.com'],
    ['name' => 'User 2', 'email' => 'user2@example.com'],
    ['name' => 'User 3', 'email' => 'user3@example.com']
];

foreach ($users as $user) {
    $params['body'][] = [
        'index' => [
            '_index' => 'users'
        ]
    ];
    
    $params['body'][] = $user;
}

$result = $client->bulk($params);

// Check for errors
if (!empty($result['errors'])) {
    // Handle errors
    foreach ($result['items'] as $item) {
        if (isset($item['index']['error'])) {
            echo 'Error: ' . $item['index']['error']['reason'] . PHP_EOL;
        }
    }
} else {
    echo 'Bulk insert completed successfully';
}
```

#### Bulk Update

```php
$params = ['body' => []];

$updates = [
    ['id' => 1, 'status' => 'inactive'],
    ['id' => 2, 'status' => 'inactive'],
    ['id' => 3, 'status' => 'inactive']
];

foreach ($updates as $update) {
    $id = $update['id'];
    unset($update['id']);
    
    $params['body'][] = [
        'update' => [
            '_index' => 'users',
            '_id' => $id
        ]
    ];
    
    $params['body'][] = [
        'doc' => $update
    ];
}

$result = $client->bulk($params);

// Check for errors
if (!empty($result['errors'])) {
    // Handle errors
} else {
    echo 'Bulk update completed successfully';
}
```

#### Bulk Delete

```php
$params = ['body' => []];

$ids = [1, 2, 3]; // IDs of documents to delete

foreach ($ids as $id) {
    $params['body'][] = [
        'delete' => [
            '_index' => 'users',
            '_id' => $id
        ]
    ];
}

$result = $client->bulk($params);

// Check for errors
if (!empty($result['errors'])) {
    // Handle errors
} else {
    echo 'Bulk delete completed successfully';
}
```

## Conclusion

The `ElasticQueryBuilder` provides a powerful and flexible way to interact with Elasticsearch. By using the fluent interface, you can build complex queries in a readable and maintainable way. For more advanced use cases, you can combine the query builder with direct calls to the Elasticsearch client.
