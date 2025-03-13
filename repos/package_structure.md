# Estrutura dos Pacotes

## jot/hf-elastic-core

### Namespace
```
Jot\HfElasticCore
```

### Estrutura de Diretórios
```
src/
  Contracts/       # Interfaces compartilhadas
  Exception/       # Exceções compartilhadas
  Provider/        # Provedores para HyperF
  Services/        # Serviços compartilhados
  ConfigProvider.php
```

### Dependências
```json
{
  "require": {
    "php": ">=8.1",
    "hyperf/framework": "3.1.*",
    "hyperf/di": "3.1.*",
    "hyperf/config": "3.1.*",
    "hyperf/command": "3.1.*",
    "hyperf/stringable": "3.1.*"
  }
}
```

## jot/hf-elastic-query

### Namespace
```
Jot\HfElasticQuery
```

### Estrutura de Diretórios
```
src/
  Facade/          # Facades para QueryBuilder
  Factories/       # Fábricas para QueryBuilder
  Query/           # Classes de construção de consultas
    Operators/     # Operadores específicos
  Repository/      # Repositórios para Elasticsearch
  ConfigProvider.php
```

### Dependências
```json
{
  "require": {
    "php": ">=8.1",
    "hyperf/framework": "3.1.*",
    "hyperf/di": "3.1.*",
    "hyperf/config": "3.1.*",
    "hyperf/stringable": "3.1.*",
    "elasticsearch/elasticsearch": "^7.0",
    "jot/hf-elastic-core": "^1.0"
  }
}
```

## jot/hf-elastic-migrations

### Namespace
```
Jot\HfElasticMigrations
```

### Estrutura de Diretórios
```
src/
  Command/         # Comandos para migrações
    stubs/         # Templates para geração de código
  Migration/       # Classes de migração
    ElasticType/   # Tipos de campos do Elasticsearch
    Helper/        # Utilitários para migrações
  Services/        # Serviços específicos para migrações
  ConfigProvider.php
```

### Dependências
```json
{
  "require": {
    "php": ">=8.1",
    "hyperf/framework": "3.1.*",
    "hyperf/di": "3.1.*",
    "hyperf/config": "3.1.*",
    "hyperf/command": "3.1.*",
    "hyperf/stringable": "3.1.*",
    "elasticsearch/elasticsearch": "^7.0",
    "jot/hf-elastic-core": "^1.0"
  }
}
```

## jot/hf-elastic (Metapacote)

### Estrutura de Diretórios
```
src/
  ConfigProvider.php  # Apenas para integrar os outros pacotes
```

### Dependências
```json
{
  "require": {
    "php": ">=8.1",
    "jot/hf-elastic-core": "^1.0",
    "jot/hf-elastic-query": "^1.0",
    "jot/hf-elastic-migrations": "^1.0"
  }
}
```
