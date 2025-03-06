# ElasticQueryBuilder Operators

This document provides details about the operators available in the ElasticQueryBuilder and how to use them effectively.

## Available Operators

The ElasticQueryBuilder supports various operators for constructing queries. These operators are implemented as strategies in the `Jot\HfElastic\Query\Operators` namespace.

### Equality Operator

The equality operator (`=`) is used to match documents where a field exactly equals a specified value.

```php
$queryBuilder->where('status', '=', 'active');
```

This translates to an Elasticsearch `term` query:

```json
{
  "term": {
    "status": "active"
  }
}
```

### Range Operators

Range operators are used to match documents where a field's value is within a specific range.

#### Greater Than (`>`)

```php
$queryBuilder->where('age', '>', 18);
```

Translates to:

```json
{
  "range": {
    "age": {
      "gt": 18
    }
  }
}
```

#### Less Than (`<`)

```php
$queryBuilder->where('price', '<', 100);
```

Translates to:

```json
{
  "range": {
    "price": {
      "lt": 100
    }
  }
}
```

#### Greater Than or Equal To (`>=`)

```php
$queryBuilder->where('quantity', '>=', 5);
```

Translates to:

```json
{
  "range": {
    "quantity": {
      "gte": 5
    }
  }
}
```

#### Less Than or Equal To (`<=`)

```php
$queryBuilder->where('rating', '<=', 5);
```

Translates to:

```json
{
  "range": {
    "rating": {
      "lte": 5
    }
  }
}
```

#### Between

The `between` operator is a special range operator that matches documents where a field's value is between two specified values (inclusive).

```php
$queryBuilder->where('price', 'between', [50, 100]);
```

Translates to:

```json
{
  "range": {
    "price": {
      "gte": 50,
      "lte": 100
    }
  }
}
```

## Combining Operators

You can combine multiple operators to create complex queries:

```php
$queryBuilder
    ->from('products')
    ->where('category', '=', 'electronics')
    ->andWhere('price', 'between', [100, 500])
    ->andWhere('stock', '>', 0)
    ->orWhere('featured', '=', true);
```

## Context-Based Operators

The ElasticQueryBuilder allows you to specify the context for each condition:

### Must Context

Conditions in the `must` context (default for `where` and `andWhere`) require that documents match the condition to be included in the results.

```php
$queryBuilder->where('status', '=', 'active', 'must');
```

### Should Context

Conditions in the `should` context (default for `orWhere`) improve the relevance score of documents that match but don't require a match.

```php
$queryBuilder->where('tags', '=', 'premium', 'should');
```

### Must Not Context

Conditions in the `must_not` context require that documents do not match the condition.

```php
$queryBuilder->where('status', '=', 'deleted', 'must_not');
```

## Custom Operators

If you need to extend the ElasticQueryBuilder with custom operators, you can create a new operator strategy by implementing the `OperatorStrategyInterface`:

```php
namespace Your\Namespace;

use Jot\HfElastic\Contracts\OperatorStrategyInterface;

class YourCustomOperator implements OperatorStrategyInterface
{
    public function apply(string $field, mixed $value, string $context): array
    {
        // Implement your custom logic here
        return ['your_custom_query' => [$field => $value]];
    }
    
    public function supports(string $operator): bool
    {
        return $operator === 'your_custom_operator';
    }
}
```

Then register your custom operator with the `OperatorRegistry`.
