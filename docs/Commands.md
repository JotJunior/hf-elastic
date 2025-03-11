# Comandos

O pacote `jot/hf-elastic` fornece uma série de comandos para gerenciar índices do Elasticsearch via linha de comando. Estes comandos são integrados ao framework Hyperf e podem ser executados usando o CLI do Hyperf.

## Instalação

Os comandos são registrados automaticamente quando o pacote é instalado. Certifique-se de que o pacote `jot/hf-elastic` está instalado em seu projeto Hyperf:

```bash
composer require jot/hf-elastic
```

## Comandos Disponíveis

### elastic:migration

Cria um novo arquivo de migration para definir ou atualizar um índice do Elasticsearch.

#### Uso

```bash
php bin/hyperf.php elastic:migration --index=nome_do_indice
```

#### Opções

- `--index` (obrigatório): O nome do índice para o qual criar a migration.
- `--update` ou `-U`: Indica que esta é uma migration de atualização para um índice existente.
- `--json-schema` ou `-S`: Caminho para um arquivo JSON Schema ou URL para gerar a migration.
- `--json` ou `-J`: Caminho para um arquivo JSON ou URL para gerar a migration.

#### Exemplos

```bash
# Criar uma nova migration para um índice
php bin/hyperf.php elastic:migration --index=users

# Criar uma migration de atualização
php bin/hyperf.php elastic:migration --index=users --update

# Criar uma migration a partir de um JSON Schema
php bin/hyperf.php elastic:migration --index=orders --json-schema=schemas/orders.json

# Criar uma migration a partir de um arquivo JSON
php bin/hyperf.php elastic:migration --index=products --json=data/products.json
```

### elastic:migrate

Aplica as migrations pendentes para criar ou atualizar índices no Elasticsearch.

#### Uso

```bash
php bin/hyperf.php elastic:migrate
```

#### Opções

- `--index` ou `-I`: Filtrar migrations por nome de índice.
- `--force` ou `-F`: Forçar a execução sem confirmação.

#### Exemplos

```bash
# Aplicar todas as migrations pendentes
php bin/hyperf.php elastic:migrate

# Aplicar migrations apenas para um índice específico
php bin/hyperf.php elastic:migrate --index=users

# Forçar a execução sem confirmação
php bin/hyperf.php elastic:migrate --force
```

### elastic:destroy

Destroi índices no Elasticsearch executando o método `down()` das migrations.

#### Uso

```bash
php bin/hyperf.php elastic:destroy
```

#### Opções

- `--index` ou `-I`: Filtrar por nome de índice.
- `--force` ou `-F`: Forçar a execução sem confirmação.

#### Exemplos

```bash
# Destruir todos os índices
php bin/hyperf.php elastic:destroy

# Destruir apenas um índice específico
php bin/hyperf.php elastic:destroy --index=users

# Forçar a destruição sem confirmação
php bin/hyperf.php elastic:destroy --force
```

### elastic:reset

Reseta os índices no Elasticsearch, destruindo-os e recriando-os em seguida.

#### Uso

```bash
php bin/hyperf.php elastic:reset
```

#### Opções

- `--index` ou `-I`: Filtrar por nome de índice.
- `--force` ou `-F`: Forçar a execução sem confirmação.

#### Exemplos

```bash
# Resetar todos os índices
php bin/hyperf.php elastic:reset

# Resetar apenas um índice específico
php bin/hyperf.php elastic:reset --index=users

# Forçar o reset sem confirmação
php bin/hyperf.php elastic:reset --force
```

## Arquitetura dos Comandos

Os comandos seguem uma arquitetura baseada em SOLID, com uma interface comum e classes especializadas:

### CommandInterface

Define o contrato para todos os comandos do pacote.

### AbstractCommand

Implementa funcionalidades comuns a todos os comandos, como:

- Verificação e criação do diretório de migrations
- Obtenção de arquivos de migration
- Formatação de mensagens de saída

### MigrationCommand

Responsável por criar novos arquivos de migration. Utiliza os serviços:

- `TemplateGenerator`: Gera o conteúdo do arquivo de migration
- `FileGenerator`: Cria o arquivo no sistema de arquivos

### MigrateCommand

Responsável por aplicar migrations pendentes. Utiliza o cliente Elasticsearch para criar ou atualizar índices.

### DestroyCommand

Responsável por destruir índices no Elasticsearch.

### ResetCommand

Combina as funcionalidades de `DestroyCommand` e `MigrateCommand` para resetar índices.

## Configuração

Os comandos utilizam a configuração definida no arquivo `config/autoload/elasticsearch.php`. Certifique-se de que este arquivo está configurado corretamente com as informações de conexão do Elasticsearch.

```php
return [
    'hosts' => [
        'http://elasticsearch:9200'
    ],
    'migrations' => [
        'directory' => BASE_PATH . '/migrations/elasticsearch',
    ],
];
```

## Exemplos de Uso em Projetos

### Fluxo de Trabalho Típico

1. Criar uma nova migration para um índice:

```bash
php bin/hyperf.php elastic:migration --index=products
```

2. Editar o arquivo de migration gerado em `migrations/elasticsearch/YYYYMMDDHHMMSS-create-products.php`.

3. Aplicar a migration para criar o índice:

```bash
php bin/hyperf.php elastic:migrate
```

4. Mais tarde, criar uma migration de atualização:

```bash
php bin/hyperf.php elastic:migration --index=products --update
```

5. Editar a nova migration e aplicar a atualização:

```bash
php bin/hyperf.php elastic:migrate
```

### Integração com CI/CD

Os comandos podem ser facilmente integrados em pipelines de CI/CD:

```yaml
# Exemplo para GitLab CI
deploy_elasticsearch:
  stage: deploy
  script:
    - php bin/hyperf.php elastic:migrate --force
  only:
    - master
```

## Dicas e Boas Práticas

1. **Versionamento**: Mantenha suas migrations em um sistema de controle de versão junto com o código da aplicação.

2. **Migrations Idempotentes**: Escreva migrations que possam ser executadas múltiplas vezes sem causar problemas.

3. **Backup**: Faça backup dos seus dados antes de executar migrations em ambiente de produção.

4. **Teste em Desenvolvimento**: Sempre teste suas migrations em ambiente de desenvolvimento antes de aplicar em produção.

5. **Documentação**: Documente as alterações feitas em cada migration para facilitar a manutenção.

6. **Migrations Pequenas**: Prefira criar múltiplas migrations pequenas e focadas em vez de uma grande migration com muitas alterações.
