# Advanced ElasticQueryBuilder Examples

This document provides advanced examples of using the ElasticQueryBuilder to construct complex Elasticsearch queries.

## Table of Contents

- [Complex Boolean Queries](#complex-boolean-queries)
- [Nested Document Queries](#nested-document-queries)
- [Geo-Spatial Queries](#geo-spatial-queries)
- [Combining Multiple Query Types](#combining-multiple-query-types)
- [Pagination and Sorting](#pagination-and-sorting)
- [Field Selection and Result Processing](#field-selection-and-result-processing)

## Complex Boolean Queries

### Example 1: Products Search with Multiple Conditions

Find electronics products that are either on sale or have a high rating, and are in stock:

```php
$results = $queryBuilder
    ->from('products')
    ->where('category', '=', 'electronics')
    ->andWhere('stock', '>', 0)
    ->whereShould(function($query) {
        $query->where('on_sale', '=', true)
              ->orWhere('rating', '>=', 4.5);
    })
    ->execute();
```

### Example 2: User Search with Exclusions

Find active users who are not in a specific role and have logged in recently:

```php
$results = $queryBuilder
    ->from('users')
    ->where('status', '=', 'active')
    ->where('role', '=', 'guest', 'must_not')
    ->where('last_login', '>=', date('Y-m-d', strtotime('-30 days')))
    ->execute();
```

### Example 3: Complex Product Filtering

Find products that match specific criteria across multiple categories:

```php
$results = $queryBuilder
    ->from('products')
    ->whereMust(function($query) {
        $query->where('status', '=', 'active')
              ->andWhere('price', 'between', [50, 200]);
    })
    ->whereShould(function($query) {
        $query->whereMust(function($subQuery) {
            $subQuery->where('category', '=', 'electronics')
                     ->andWhere('brand', '=', 'Samsung');
        });
        $query->whereMust(function($subQuery) {
            $subQuery->where('category', '=', 'appliances')
                     ->andWhere('energy_rating', '>=', 'A');
        });
    })
    ->limit(20)
    ->execute();
```

## Nested Document Queries

### Example 1: Order Search with Line Items

Find orders that contain a specific product with a quantity greater than 2:

```php
$results = $queryBuilder
    ->from('orders')
    ->whereNested('line_items', function($query) {
        $query->where('product_id', '=', 123)
              ->andWhere('quantity', '>', 2);
    })
    ->execute();
```

### Example 2: User Search with Address Filtering

Find users who live in a specific city and have a verified address:

```php
$results = $queryBuilder
    ->from('users')
    ->whereNested('address', function($query) {
        $query->where('city', '=', 'New York')
              ->andWhere('verified', '=', true);
    })
    ->execute();
```

### Example 3: Complex Nested Query with Multiple Conditions

Find blog posts with comments from a specific user or containing specific keywords:

```php
$results = $queryBuilder
    ->from('blog_posts')
    ->where('status', '=', 'published')
    ->whereNested('comments', function($query) {
        $query->whereShould(function($subQuery) {
            $subQuery->where('user_id', '=', 456);
            $subQuery->where('content', 'like', 'great article');
        });
    })
    ->execute();
```

## Geo-Spatial Queries

### Example 1: Find Locations Within a Radius

Find restaurants within 5km of a specific location:

```php
$results = $queryBuilder
    ->from('restaurants')
    ->geoDistance('location', '40.7128,-74.0060', '5km')
    ->execute();
```

### Example 2: Geo Distance with Additional Filters

Find hotels within 10km of a location, with specific amenities and price range:

```php
$results = $queryBuilder
    ->from('hotels')
    ->geoDistance('location', '34.0522,-118.2437', '10km')
    ->where('price_per_night', 'between', [100, 300])
    ->whereNested('amenities', function($query) {
        $query->where('name', '=', 'pool')
              ->orWhere('name', '=', 'spa');
    })
    ->execute();
```

## Combining Multiple Query Types

### Example: Complex Search with Multiple Features

Find products that match specific criteria, are near a location, and have nested attributes:

```php
$results = $queryBuilder
    ->from('products')
    ->where('category', '=', 'furniture')
    ->andWhere('price', 'between', [200, 1000])
    ->whereShould(function($query) {
        $query->where('brand', '=', 'IKEA')
              ->orWhere('brand', '=', 'Herman Miller');
    })
    ->whereNested('attributes', function($query) {
        $query->where('name', '=', 'material')
              ->andWhere('value', '=', 'wood');
    })
    ->geoDistance('warehouse_location', '37.7749,-122.4194', '50km')
    ->limit(10)
    ->orderBy('price', 'asc')
    ->execute();
```

## Pagination and Sorting

### Example 1: Paginated Results with Multiple Sort Criteria

```php
$page = 2;
$perPage = 15;

$results = $queryBuilder
    ->from('products')
    ->where('category', '=', 'clothing')
    ->limit($perPage)
    ->offset(($page - 1) * $perPage)
    ->orderBy('popularity', 'desc')
    ->orderBy('price', 'asc')
    ->execute();
```

### Example 2: Efficient Pagination with Count

```php
$page = 1;
$perPage = 20;

// Get total count first
$totalCount = $queryBuilder
    ->from('products')
    ->where('category', '=', 'books')
    ->count();

// Then get paginated results
$results = $queryBuilder
    ->from('products')
    ->where('category', '=', 'books')
    ->limit($perPage)
    ->offset(($page - 1) * $perPage)
    ->orderBy('published_date', 'desc')
    ->execute();

$totalPages = ceil($totalCount / $perPage);
```

## Field Selection and Result Processing

### Example 1: Selecting Specific Fields

```php
$results = $queryBuilder
    ->from('users')
    ->select(['id', 'name', 'email', 'created_at'])
    ->where('status', '=', 'active')
    ->execute();

// Process the results
foreach ($results['data'] as $user) {
    echo "User: {$user['name']} ({$user['email']})\n";
}
```

### Example 2: Advanced Result Processing with Error Handling

```php
$results = $queryBuilder
    ->from('orders')
    ->where('status', '=', 'pending')
    ->andWhere('created_at', '>=', date('Y-m-d', strtotime('-7 days')))
    ->execute();

if ($results['result'] === 'success') {
    $pendingOrders = $results['data'];
    
    // Process the orders
    foreach ($pendingOrders as $order) {
        // Do something with each order
        processOrder($order);
    }
    
    echo "Processed " . count($pendingOrders) . " pending orders.";
} else {
    echo "Error: " . $results['error'];
}
```

## Conclusion

These advanced examples demonstrate the flexibility and power of the ElasticQueryBuilder. By combining different query types and methods, you can construct complex Elasticsearch queries in a readable and maintainable way.
