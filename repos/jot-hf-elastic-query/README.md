# Jot HF Elastic Query

## Descriu00e7u00e3o

Este pacote fornece componentes para construu00e7u00e3o de consultas ao Elasticsearch no framework HyperF 3.1. Ele depende do pacote `jot/hf-elastic-core` e fornece uma API fluente para construu00e7u00e3o de consultas complexas.

## Instalau00e7u00e3o

```bash
composer require jot/hf-elastic-query
```

## Configurau00e7u00e3o

Publique o arquivo de configurau00e7u00e3o:

```bash
php bin/hyperf.php vendor:publish jot/hf-elastic-query
```

Edite o arquivo de configurau00e7u00e3o em `config/autoload/hf_elastic_query.php` para configurar o comportamento do QueryBuilder:

```php
return [
    'query_builder' => [
        'default_size' => 10,
        'max_size' => 10000,
        'operators' => [
            'equals' => \Jot\HfElasticQuery\Query\Operators\EqualsOperator::class,
            'not_equals' => \Jot\HfElasticQuery\Query\Operators\NotEqualsOperator::class,
            'range' => \Jot\HfElasticQuery\Query\Operators\RangeOperator::class,
        ],
    ],
];
```

## Componentes

### Query

O pacote fornece classes para construu00e7u00e3o de consultas:

- `ElasticQueryBuilder`: Implementau00e7u00e3o principal do QueryBuilder
- `OperatorRegistry`: Registro de operadores de consulta
- `QueryContext`: Contexto para execuu00e7u00e3o de consultas

### Operators

Operadores para construu00e7u00e3o de consultas:

- `EqualsOperator`: Operador de igualdade
- `NotEqualsOperator`: Operador de desigualdade
- `RangeOperator`: Operador de intervalo

### Repository

Repositu00f3rio para interau00e7u00e3o com o Elasticsearch:

- `ElasticRepository`: Implementau00e7u00e3o do repositu00f3rio para Elasticsearch

### Facade

Facade para facilitar o uso do QueryBuilder:

- `QueryBuilder`: Facade para o QueryBuilder

## Exemplo de Uso

```php
<?php

use Jot\HfElasticQuery\Facade\QueryBuilder;

// Buscar documentos com campo 'name' igual a 'John'
$results = QueryBuilder::index('users')
    ->where('name', 'John')
    ->get();

// Buscar documentos com campo 'age' entre 18 e 30
$results = QueryBuilder::index('users')
    ->where('age', '>=', 18)
    ->where('age', '<=', 30)
    ->get();

// Buscar documentos com paginau00e7u00e3o
$results = QueryBuilder::index('users')
    ->where('active', true)
    ->orderBy('created_at', 'desc')
    ->paginate(10, 1); // 10 itens por pu00e1gina, pu00e1gina 1
```

## Requisitos

- PHP >= 8.1
- HyperF Framework 3.1.*
- jot/hf-elastic-core ^1.0

## Licenu00e7a

MIT
