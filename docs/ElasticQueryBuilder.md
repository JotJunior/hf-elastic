# ElasticQueryBuilder

O `ElasticQueryBuilder` é uma implementação da interface `QueryBuilderInterface` que facilita a construção de consultas para o Elasticsearch. Ele fornece uma API fluente para criar consultas complexas de forma intuitiva.

## Instalação

O `ElasticQueryBuilder` é parte do pacote `jot/hf-elastic`. Certifique-se de que o pacote está instalado em seu projeto Hyperf.

```bash
composer require jot/hf-elastic
```

## Uso Básico

### Inicialização

O `ElasticQueryBuilder` pode ser obtido através do container de injeção de dependência do Hyperf:

```php
$queryBuilder = $container->get(\Jot\HfElastic\Query\ElasticQueryBuilder::class);
```

Ou você pode injetá-lo diretamente em suas classes:

```php
public function __construct(\Jot\HfElastic\Query\ElasticQueryBuilder $queryBuilder)
{
    $this->queryBuilder = $queryBuilder;
}
```

### Consultas Simples

```php
// Buscar todos os documentos do índice 'users'
$result = $queryBuilder->from('users')->execute();

// Buscar documentos com condições
$result = $queryBuilder
    ->from('users')
    ->where('name', 'John')
    ->execute();

// Buscar com múltiplas condições
$result = $queryBuilder
    ->from('users')
    ->where('age', '>=', 18)
    ->andWhere('status', 'active')
    ->execute();
```

### Consultas Avançadas

```php
// Consulta com OR
$result = $queryBuilder
    ->from('users')
    ->where('name', 'John')
    ->orWhere('name', 'Jane')
    ->execute();

// Consulta com ordenação
$result = $queryBuilder
    ->from('users')
    ->orderBy('created_at', 'desc')
    ->execute();

// Consulta com paginação
$result = $queryBuilder
    ->from('users')
    ->limit(10)
    ->offset(0)
    ->execute();

// Consulta com campos específicos
$result = $queryBuilder
    ->from('users')
    ->select(['id', 'name', 'email'])
    ->execute();
```

### Consultas Geoespaciais

```php
// Buscar documentos por distância geográfica
$result = $queryBuilder
    ->from('locations')
    ->geoDistance('position', '40.715, -74.011', '5km')
    ->execute();
```

### Consultas Aninhadas

```php
// Consulta com condições aninhadas
$result = $queryBuilder
    ->from('users')
    ->whereNested('address', function ($query) {
        $query->where('city', 'New York');
    })
    ->execute();

// Consulta com condições MUST (AND lógico)
$result = $queryBuilder
    ->from('users')
    ->whereMust(function ($query) {
        $query->where('status', 'active');
        $query->where('age', '>=', 18);
    })
    ->execute();

// Consulta com condições SHOULD (OR lógico)
$result = $queryBuilder
    ->from('users')
    ->whereShould(function ($query) {
        $query->where('role', 'admin');
        $query->where('role', 'manager');
    })
    ->execute();
```

### Contagem de Documentos

```php
// Contar documentos que atendem a uma condição
$count = $queryBuilder
    ->from('users')
    ->where('status', 'active')
    ->count();
```

### Múltiplos Índices

```php
// Buscar em múltiplos índices
$result = $queryBuilder
    ->from('users')
    ->join(['profiles', 'settings'])
    ->execute();
```

## Operadores Suportados

O `ElasticQueryBuilder` suporta os seguintes operadores:

- `=` ou `==`: Igualdade
- `!=` ou `<>`: Diferença
- `>`: Maior que
- `>=`: Maior ou igual a
- `<`: Menor que
- `<=`: Menor ou igual a
- `between`: Entre dois valores

## Tratamento de Erros

O `ElasticQueryBuilder` captura exceções do Elasticsearch e as converte em mensagens de erro mais amigáveis. Você pode capturar essas exceções em seu código:

```php
try {
    $result = $queryBuilder->from('users')->execute();
} catch (\Exception $e) {
    echo "Erro na consulta: " . $e->getMessage();
}
```

## Métodos Disponíveis

### Métodos de Seleção de Índice

- `from(string $index)`: Define o índice principal para a consulta
- `into(string $index)`: Alias para `from()`
- `join(string|array $index)`: Adiciona índices adicionais à consulta

### Métodos de Condição

- `where(string $field, mixed $operator, mixed $value = null, string $context = 'must')`: Adiciona uma condição à consulta
- `andWhere(string $field, mixed $operator, mixed $value = null, string $context = 'must')`: Adiciona uma condição AND à consulta
- `orWhere(string $field, mixed $operator, mixed $value = null, string $subContext = 'should')`: Adiciona uma condição OR à consulta
- `whereMust(callable $callback)`: Adiciona condições aninhadas com contexto MUST
- `whereShould(callable $callback)`: Adiciona condições aninhadas com contexto SHOULD
- `whereNested(string $path, callable $callback)`: Adiciona condições para campos aninhados

### Métodos de Paginação e Ordenação

- `limit(int $limit)`: Define o número máximo de resultados
- `offset(int $offset)`: Define o deslocamento para paginação
- `orderBy(string $field, string $order = 'asc')`: Define a ordenação dos resultados

### Métodos de Seleção de Campos

- `select(string|array $fields = '*')`: Define os campos a serem retornados

### Métodos Geoespaciais

- `geoDistance(string $field, string $location, string $distance)`: Adiciona uma condição de distância geográfica

### Métodos de Execução

- `execute()`: Executa a consulta e retorna os resultados
- `count()`: Conta o número de documentos que correspondem à consulta
- `toArray()`: Converte a consulta em um array para inspeção

## Exemplos Completos

### Exemplo 1: Busca de Usários com Filtros e Paginação

```php
$users = $queryBuilder
    ->from('users')
    ->where('status', 'active')
    ->andWhere('age', '>=', 18)
    ->andWhere('created_at', '>=', '2023-01-01')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->offset(0)
    ->execute();
```

### Exemplo 2: Busca Geoespacial com Filtros Adicionais

```php
$restaurants = $queryBuilder
    ->from('restaurants')
    ->geoDistance('location', '40.715, -74.011', '1km')
    ->andWhere('rating', '>=', 4)
    ->andWhere('cuisine', 'italian')
    ->orderBy('rating', 'desc')
    ->execute();
```

### Exemplo 3: Busca com Condições Complexas

```php
$products = $queryBuilder
    ->from('products')
    ->whereMust(function ($query) {
        $query->where('status', 'in_stock');
        $query->where('price', '<=', 100);
    })
    ->whereShould(function ($query) {
        $query->where('category', 'electronics');
        $query->where('category', 'computers');
    })
    ->whereNested('reviews', function ($query) {
        $query->where('rating', '>=', 4);
    })
    ->orderBy('popularity', 'desc')
    ->limit(20)
    ->execute();
```
