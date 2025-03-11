# Migrations

As migrations no pacote `jot/hf-elastic` são usadas para gerenciar a criação e atualização de índices no Elasticsearch. Elas fornecem uma maneira estruturada e versionada de definir e modificar mapeamentos de índices.

## Conceitos Básicos

### O que são Migrations?

Migrations são arquivos PHP que definem a estrutura dos índices do Elasticsearch. Cada migration representa uma mudança específica na estrutura do índice, como criação de um novo índice ou atualização de um existente.

### Estrutura de Diretórios

Por padrão, as migrations são armazenadas no diretório `migrations/elasticsearch` na raiz do seu projeto.

## Criando Migrations

### Usando o Comando de Migration

O pacote fornece um comando para gerar arquivos de migration:

```bash
php bin/hyperf.php elastic:migration --index=nome_do_indice
```

Para criar uma migration de atualização:

```bash
php bin/hyperf.php elastic:migration --index=nome_do_indice --update
```

### Usando JSON Schema

Você pode gerar uma migration a partir de um arquivo JSON Schema:

```bash
php bin/hyperf.php elastic:migration --index=nome_do_indice --json-schema=caminho/para/schema.json
```

### Usando JSON

Também é possível gerar uma migration a partir de um arquivo JSON:

```bash
php bin/hyperf.php elastic:migration --index=nome_do_indice --json=caminho/para/dados.json
```

## Estrutura de uma Migration

Uma migration típica tem a seguinte estrutura:

```php
<?php

use Jot\HfElastic\Migration\Mapping;
use Jot\HfElastic\Migration\ElasticType\Type;

return new class {
    public function up(): array
    {
        $mapping = new Mapping('nome_do_indice');
        
        // Configurar settings do índice
        $mapping->settings([
            'number_of_shards' => 1,
            'number_of_replicas' => 1,
        ]);
        
        // Definir campos
        $mapping->property('id', Type::keyword);
        $mapping->property('name', Type::text);
        $mapping->property('email', Type::keyword);
        $mapping->property('created_at', Type::date);
        
        return $mapping->body();
    }
    
    public function down(): array
    {
        return ['index' => 'nome_do_indice'];
    }
};
```

### Método `up()`

O método `up()` define as ações a serem executadas quando a migration é aplicada. Para uma migration de criação, ele retorna a definição completa do índice. Para uma migration de atualização, ele retorna apenas as alterações a serem aplicadas.

### Método `down()`

O método `down()` define as ações a serem executadas quando a migration é revertida. Geralmente, para uma migration de criação, ele retorna informações para excluir o índice.

## Classe Mapping

A classe `Mapping` é o componente central para definir a estrutura de um índice. Ela fornece métodos para configurar settings e definir campos.

### Construtor

```php
$mapping = new Mapping('nome_do_indice', 'strict');
```

O segundo parâmetro define o comportamento dinâmico do mapeamento (strict, true, false, runtime).

### Settings

```php
$mapping->settings([
    'number_of_shards' => 1,
    'number_of_replicas' => 1,
    'analysis' => [
        'analyzer' => [
            'my_analyzer' => [
                'type' => 'custom',
                'tokenizer' => 'standard',
                'filter' => ['lowercase', 'asciifolding']
            ]
        ]
    ]
]);
```

### Definindo Campos

#### Campos Básicos

```php
// Campo de texto
$mapping->property('title', Type::text);

// Campo de palavra-chave
$mapping->property('tag', Type::keyword);

// Campo numérico
$mapping->property('price', Type::float);
$mapping->property('quantity', Type::integer);

// Campo de data
$mapping->property('created_at', Type::date);

// Campo booleano
$mapping->property('active', Type::boolean);
```

#### Campos com Opções

```php
// Campo de texto com opções
$mapping->property('description', Type::text, [
    'analyzer' => 'my_analyzer',
    'fields' => [
        'keyword' => [
            'type' => 'keyword',
            'ignore_above' => 256
        ]
    ]
]);
```

#### Campos Aninhados

```php
// Campo aninhado
$nested = $mapping->nested('comments');
$nested->property('author', Type::keyword);
$nested->property('text', Type::text);
$nested->property('date', Type::date);
```

#### Campos de Objeto

```php
// Campo de objeto
$object = $mapping->object('address');
$object->property('street', Type::text);
$object->property('city', Type::keyword);
$object->property('country', Type::keyword);
$object->property('zip', Type::keyword);
```

#### Campos Geoespaciais

```php
// Campo de ponto geográfico
$mapping->property('location', Type::geoPoint);

// Campo de forma geográfica
$mapping->property('area', Type::geoShape);
```

#### Campos Especializados

```php
// Campo de pesquisa enquanto digita
$mapping->searchAsYouType('product_name')
    ->analyzer('standard')
    ->maxShingleSize(3);

// Campo de métrica agregada
$mapping->aggregateMetricDouble('price_stats', ['min', 'max', 'sum'])
    ->defaultMetric('sum');
```

## Executando Migrations

Para aplicar as migrations pendentes:

```bash
php bin/hyperf.php elastic:migrate
```

Para aplicar migrations para um índice específico:

```bash
php bin/hyperf.php elastic:migrate --index=nome_do_indice
```

## Revertendo Migrations

Para destruir índices (executar o método `down()` das migrations):

```bash
php bin/hyperf.php elastic:destroy
```

Para destruir um índice específico:

```bash
php bin/hyperf.php elastic:destroy --index=nome_do_indice
```

## Resetando Migrations

Para resetar as migrations (destruir e recriar índices):

```bash
php bin/hyperf.php elastic:reset
```

Para resetar um índice específico:

```bash
php bin/hyperf.php elastic:reset --index=nome_do_indice
```

## Tipos de Campo Suportados

O pacote suporta todos os tipos de campo do Elasticsearch através do enum `Type`:

- `Type::text`: Texto completo, analisado e pesquisável
- `Type::keyword`: Texto exato, não analisado
- `Type::long`, `Type::integer`, `Type::short`, `Type::byte`: Tipos inteiros
- `Type::double`, `Type::float`, `Type::halfFloat`, `Type::scaledFloat`: Tipos de ponto flutuante
- `Type::date`: Data e hora
- `Type::boolean`: Valores booleanos
- `Type::binary`: Dados binários codificados em base64
- `Type::geoPoint`: Coordenadas geográficas
- `Type::geoShape`: Formas geográficas
- `Type::ip`: Endereços IP
- `Type::completion`: Sugestões de autocompletar
- `Type::object`: Objetos JSON aninhados
- `Type::nested`: Arrays de objetos para consultas aninhadas
- `Type::searchAsYouType`: Campo otimizado para pesquisa enquanto digita
- `Type::denseVector`: Vetores densos para pesquisa de similaridade
- E muitos outros

## Exemplos Completos

### Exemplo 1: u00cdndice de Produtos

```php
<?php

use Jot\HfElastic\Migration\Mapping;
use Jot\HfElastic\Migration\ElasticType\Type;

return new class {
    public function up(): array
    {
        $mapping = new Mapping('products');
        
        $mapping->settings([
            'number_of_shards' => 3,
            'number_of_replicas' => 1,
            'analysis' => [
                'analyzer' => [
                    'product_analyzer' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => ['lowercase', 'asciifolding']
                    ]
                ]
            ]
        ]);
        
        $mapping->property('id', Type::keyword);
        $mapping->searchAsYouType('name')->analyzer('product_analyzer');
        $mapping->property('description', Type::text, ['analyzer' => 'product_analyzer']);
        $mapping->property('sku', Type::keyword);
        $mapping->property('price', Type::float);
        $mapping->property('stock', Type::integer);
        $mapping->property('created_at', Type::date);
        $mapping->property('updated_at', Type::date);
        
        $categories = $mapping->nested('categories');
        $categories->property('id', Type::keyword);
        $categories->property('name', Type::keyword);
        
        $attributes = $mapping->nested('attributes');
        $attributes->property('name', Type::keyword);
        $attributes->property('value', Type::keyword);
        
        return $mapping->body();
    }
    
    public function down(): array
    {
        return ['index' => 'products'];
    }
};
```

### Exemplo 2: u00cdndice de Usuários com Endereços

```php
<?php

use Jot\HfElastic\Migration\Mapping;
use Jot\HfElastic\Migration\ElasticType\Type;

return new class {
    public function up(): array
    {
        $mapping = new Mapping('users');
        
        $mapping->settings([
            'number_of_shards' => 2,
            'number_of_replicas' => 1
        ]);
        
        $mapping->property('id', Type::keyword);
        $mapping->property('name', Type::text, [
            'fields' => [
                'keyword' => ['type' => 'keyword']
            ]
        ]);
        $mapping->property('email', Type::keyword);
        $mapping->property('phone', Type::keyword);
        $mapping->property('created_at', Type::date);
        $mapping->property('active', Type::boolean);
        
        $address = $mapping->object('address');
        $address->property('street', Type::text);
        $address->property('number', Type::keyword);
        $address->property('city', Type::keyword);
        $address->property('state', Type::keyword);
        $address->property('country', Type::keyword);
        $address->property('zip', Type::keyword);
        $address->property('location', Type::geoPoint);
        
        return $mapping->body();
    }
    
    public function down(): array
    {
        return ['index' => 'users'];
    }
};
```

### Exemplo 3: Atualização de um u00cdndice Existente

```php
<?php

use Jot\HfElastic\Migration\Mapping;
use Jot\HfElastic\Migration\ElasticType\Type;

return new class {
    public function up(): array
    {
        $mapping = new Mapping('products');
        
        // Adicionar novos campos
        $mapping->property('discount_price', Type::float);
        $mapping->property('is_on_sale', Type::boolean);
        
        // Adicionar campo de vetores para pesquisa de similaridade
        $mapping->property('feature_vector', Type::denseVector, [
            'dims' => 128
        ]);
        
        return $mapping->updateBody();
    }
    
    public function down(): array
    {
        // Não é possível remover campos no Elasticsearch,
        // então geralmente não fazemos nada aqui
        return ['index' => 'products'];
    }
};
```
