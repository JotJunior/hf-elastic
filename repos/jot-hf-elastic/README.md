# Jot HF Elastic

## Descriu00e7u00e3o

Este u00e9 um metapacote que integra os seguintes componentes para integrar o Elasticsearch com o framework HyperF 3.1:

- `jot/hf-elastic-core`: Componentes bu00e1sicos e interfaces
- `jot/hf-elastic-query`: Componentes para construu00e7u00e3o de consultas
- `jot/hf-elastic-migrations`: Componentes para gerenciamento de migrau00e7u00f5es

Este pacote mantém compatibilidade com versões anteriores do `jot/hf-elastic` através de aliases de namespace, permitindo uma migração gradual para a nova estrutura modular.

## Instalau00e7u00e3o

```bash
composer require jot/hf-elastic
```

## Configurau00e7u00e3o

Publique os arquivos de configurau00e7u00e3o de todos os pacotes:

```bash
php bin/hyperf.php vendor:publish jot/hf-elastic-core
php bin/hyperf.php vendor:publish jot/hf-elastic-query
php bin/hyperf.php vendor:publish jot/hf-elastic-migrations
```

## Componentes

Este metapacote inclui todos os componentes dos pacotes individuais:

### Core

- Interfaces e contratos
- Exceu00e7u00f5es
- Serviu00e7os compartilhados

### Query

- QueryBuilder para construu00e7u00e3o de consultas
- Operadores para consultas
- Repositu00f3rio para interau00e7u00e3o com o Elasticsearch

### Migrations

- Comandos para gerenciamento de migrau00e7u00f5es
- Classes para definiu00e7u00e3o de mapeamentos
- Tipos de campos do Elasticsearch

## Compatibilidade com Versu00f5es Anteriores

Este pacote mantém compatibilidade com código existente através de aliases de namespace. Por exemplo:

```php
// Código antigo continua funcionando
use Jot\HfElastic\Query\ElasticQueryBuilder;

// Novo código pode usar os namespaces específicos
use Jot\HfElasticQuery\Query\ElasticQueryBuilder;
```

## Migrau00e7u00e3o para a Nova Estrutura

Recomendamos a migrau00e7u00e3o gradual para os novos pacotes:

1. Atualize para a versu00e3o mais recente do metapacote `jot/hf-elastic`
2. Gradualmente atualize as referu00eancias de namespace em seu cu00f3digo
3. Quando todas as referu00eancias forem atualizadas, considere usar apenas os pacotes especu00edficos que seu projeto necessita

## Requisitos

- PHP >= 8.1
- HyperF Framework 3.1.*

## Licenu00e7a

MIT
