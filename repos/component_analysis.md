# Análise de Componentes do Pacote jot/hf-elastic

## Categorização de Componentes

### Core
- `src/ClientBuilder.php` - Construtor de cliente Elasticsearch
- `src/ConfigProvider.php` - Provedor de configuração para o framework HyperF
- `src/Contracts/` - Interfaces base do sistema
  - `ClientFactoryInterface.php`
  - `CommandInterface.php`
  - `ElasticRepositoryInterface.php`
  - `MappingGeneratorInterface.php`
  - `MappingInterface.php`
  - `MigrationInterface.php`
  - `OperatorStrategyInterface.php`
  - `PropertyInterface.php`
  - `QueryBuilderInterface.php`
- `src/Exception/` - Exceções comuns
  - `DocumentExistsException.php`
  - `MissingCredentialsException.php`
  - `MissingMigrationDirectoryException.php`
- `src/Provider/ElasticServiceProvider.php` - Provedor de serviços para o framework HyperF
- `src/Services/IndexNameFormatter.php` - Serviço de formatação de nomes de índices

### QueryBuilder
- `src/Facade/QueryBuilder.php` - Facade para o QueryBuilder
- `src/Factories/QueryBuilderFactory.php` - Fábrica para criação de QueryBuilder
- `src/Query/` - Componentes de construção de consultas
  - `ElasticQueryBuilder.php` - Implementação principal do QueryBuilder
  - `OperatorRegistry.php` - Registro de operadores de consulta
  - `Operators/` - Operadores específicos para consultas
    - `EqualsOperator.php`
    - `NotEqualsOperator.php`
    - `RangeOperator.php`
  - `QueryContext.php` - Contexto para execução de consultas
- `src/QueryBuilder.php` - Classe principal do QueryBuilder
- `src/Repository/ElasticRepository.php` - Repositório para interação com o Elasticsearch

### Migrations
- `src/Command/` - Comandos para gerenciamento de migrações
  - `AbstractCommand.php` - Classe base para comandos
  - `DestroyCommand.php` - Comando para destruir índices
  - `MigrateCommand.php` - Comando para executar migrações
  - `MigrationCommand.php` - Comando para criar migrações
  - `ResetCommand.php` - Comando para resetar migrações
  - `stubs/` - Templates para geração de código
- `src/Migration.php` - Classe principal de migração
- `src/Migration/` - Componentes de migração
  - `AbstractField.php` - Classe base para campos
  - `ElasticType/` - Tipos de campos do Elasticsearch
    - Diversos tipos específicos (TextType, KeywordType, etc.)
  - `FieldInterface.php` - Interface para campos
  - `Helper/` - Utilitários para migrações
    - `Json.php`
    - `JsonSchema.php`
  - `Mapping.php` - Mapeamento de índices
  - `Property.php` - Propriedades de índices
- `src/Services/` - Serviços relacionados a migrações
  - `FileGenerator.php` - Gerador de arquivos
  - `TemplateGenerator.php` - Gerador de templates

## Dependências entre Componentes

### Core -> QueryBuilder
- `ElasticServiceProvider` depende de `QueryBuilderInterface`, `ElasticQueryBuilder`, `OperatorRegistry`, etc.
- `ConfigProvider` depende de `QueryBuilderInterface`, `ElasticQueryBuilder`, etc.

### Core -> Migrations
- `ConfigProvider` depende de comandos de migração
- `ElasticServiceProvider` não possui dependências diretas para Migrations

### QueryBuilder -> Core
- `ElasticQueryBuilder` depende de `ClientFactoryInterface`, `QueryBuilderInterface`
- `OperatorRegistry` depende de `OperatorStrategyInterface`
- `ElasticRepository` depende de `ElasticRepositoryInterface`, `QueryBuilderInterface`

### QueryBuilder -> Migrations
- Não há dependências diretas

### Migrations -> Core
- `AbstractCommand` depende de `CommandInterface`
- `Migration` depende de `MigrationInterface`
- `Mapping` depende de `MappingInterface`

### Migrations -> QueryBuilder
- Não há dependências diretas
