# Jot HF Elastic Core

## Descrição

Este pacote fornece os componentes básicos para integração com Elasticsearch no framework HyperF 3.1. Ele contém interfaces, exceções e serviços compartilhados que são utilizados pelos pacotes `jot/hf-elastic-query` e `jot/hf-elastic-migrations`.

## Instalação

```bash
composer require jot/hf-elastic-core
```

## Configuração

Publique o arquivo de configuração:

```bash
php bin/hyperf.php vendor:publish jot/hf-elastic-core
```

Edite o arquivo de configuração em `config/autoload/hf_elastic_core.php` para configurar a conexão com o Elasticsearch:

```php
return [
    'elasticsearch' => [
        'hosts' => [
            'http://localhost:9200',
        ],
        'retries' => 2,
        'username' => '',
        'password' => '',
        'index_prefix' => '',
    ],
];
```

## Componentes

### Contracts

O pacote fornece interfaces para os principais componentes do sistema:

- `ClientFactoryInterface`: Interface para fábricas de clientes Elasticsearch
- `CommandInterface`: Interface para comandos do console
- `ElasticRepositoryInterface`: Interface para repositórios Elasticsearch
- `MappingGeneratorInterface`: Interface para geradores de mapeamento
- `MappingInterface`: Interface para mapeamentos de índices
- `MigrationInterface`: Interface para migrações
- `OperatorStrategyInterface`: Interface para estratégias de operadores de consulta
- `PropertyInterface`: Interface para propriedades de mapeamento
- `QueryBuilderInterface`: Interface para construtores de consulta

### Exceptions

Exceções específicas para operações com Elasticsearch:

- `DocumentExistsException`: Lançada quando um documento já existe
- `MissingCredentialsException`: Lançada quando credenciais estão faltando
- `MissingMigrationDirectoryException`: Lançada quando o diretório de migrações não existe

### Services

Serviços compartilhados:

- `IndexNameFormatter`: Serviço para formatação de nomes de índices

## Requisitos

- PHP >= 8.1
- HyperF Framework 3.1.*

## Licença

MIT
