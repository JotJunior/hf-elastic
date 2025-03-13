# Jot HF Elastic Migrations

## Descriu00e7u00e3o

Este pacote fornece componentes para gerenciamento de migrau00e7u00f5es de u00edndices no Elasticsearch para o framework HyperF 3.1. Ele depende do pacote `jot/hf-elastic-core` e permite criar, atualizar e gerenciar u00edndices do Elasticsearch de forma estruturada.

## Instalau00e7u00e3o

```bash
composer require jot/hf-elastic-migrations
```

## Configurau00e7u00e3o

Publique o arquivo de configurau00e7u00e3o:

```bash
php bin/hyperf.php vendor:publish jot/hf-elastic-migrations
```

Edite o arquivo de configurau00e7u00e3o em `config/autoload/hf_elastic_migrations.php` para configurar o comportamento das migrau00e7u00f5es:

```php
return [
    'migrations' => [
        'path' => BASE_PATH . '/migrations',
        'table' => 'migrations',
        'namespace' => 'App\\Migrations',
    ],
];
```

## Comandos

O pacote fornece os seguintes comandos para gerenciamento de migrau00e7u00f5es:

- `php bin/hyperf.php elastic:migration {name}`: Cria um novo arquivo de migrau00e7u00e3o
- `php bin/hyperf.php elastic:migrate`: Executa as migrau00e7u00f5es pendentes
- `php bin/hyperf.php elastic:reset`: Reseta todos os u00edndices (remove e recria)
- `php bin/hyperf.php elastic:destroy`: Remove todos os u00edndices

## Componentes

### Migration

O pacote fornece classes para definiu00e7u00e3o de migrau00e7u00f5es:

- `Migration`: Classe base para migrau00e7u00f5es
- `Mapping`: Classe para definiu00e7u00e3o de mapeamentos
- `Property`: Classe para definiu00e7u00e3o de propriedades

### ElasticType

Tipos de campos suportados pelo Elasticsearch:

- `TextType`: Tipo para texto completo
- `KeywordType`: Tipo para palavras-chave
- `BooleanType`: Tipo para valores booleanos
- `DateType`: Tipo para datas
- `DoubleType`: Tipo para nu00fameros de ponto flutuante
- `FloatType`: Tipo para nu00fameros de ponto flutuante
- `IntegerType`: Tipo para nu00fameros inteiros
- `LongType`: Tipo para nu00fameros inteiros longos
- `NestedType`: Tipo para objetos aninhados
- `ObjectType`: Tipo para objetos
- `GeoPointType`: Tipo para coordenadas geogru00e1ficas
- `GeoShapeType`: Tipo para formas geogru00e1ficas
- `IpType`: Tipo para endereu00e7os IP
- `CompletionType`: Tipo para sugestu00f5es de autocompletar
- `SearchAsYouType`: Tipo para busca enquanto digita

## Exemplo de Uso

### Criando uma Migrau00e7u00e3o

```bash
php bin/hyperf.php elastic:migration CreateUsersIndex
```

### Definindo o Mapeamento

```php
<?php

use Hyperf\Context\ApplicationContext;
use Jot\HfElasticMigrations\Migration;
use Jot\HfElasticMigrations\Migration\Mapping;

class CreateUsersIndex extends Migration
{
    public function up(): void
    {
        $this->createIndex('users', function (Mapping $mapping) {
            $mapping->text('name');
            $mapping->keyword('email');
            $mapping->integer('age');
            $mapping->boolean('active');
            $mapping->date('created_at');
        });
    }

    public function down(): void
    {
        $this->dropIndex('users');
    }
}
```

### Executando Migrau00e7u00f5es

```bash
php bin/hyperf.php elastic:migrate
```

## Requisitos

- PHP >= 8.1
- HyperF Framework 3.1.*
- jot/hf-elastic-core ^1.0

## Licenu00e7a

MIT
