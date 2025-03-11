# Documentação do jot/hf-elastic

Bem-vindo à documentação oficial do pacote `jot/hf-elastic`, uma solução completa para integrar o Elasticsearch com aplicações PHP baseadas no framework Hyperf.

## Conteúdo

- [ElasticQueryBuilder](ElasticQueryBuilder.md): Guia completo sobre o uso do ElasticQueryBuilder
- [Migrations](Migrations.md): Documentação sobre o sistema de migrations
- [Commands](Commands.md): Referência dos comandos disponíveis

## Sobre o jot/hf-elastic

O pacote `jot/hf-elastic` foi desenvolvido para simplificar a integração do Elasticsearch com aplicações Hyperf, fornecendo:

- Uma API fluente para construção de consultas
- Um sistema de migrations para gerenciar a estrutura dos índices
- Comandos para gerenciar índices via linha de comando
- Suporte para todos os tipos de campo do Elasticsearch
- Integração com o sistema de injeção de dependência do Hyperf

## Instalação

```bash
composer require jot/hf-elastic
```

## Configuração

Após a instalação, crie um arquivo de configuração em `config/autoload/elasticsearch.php`:

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

## Uso Básico

### Consulta Simples

```php
$result = $this->container->get(\Jot\HfElastic\Query\ElasticQueryBuilder::class)
    ->from('users')
    ->where('name', 'John')
    ->execute();
```

### Criar uma Migration

```bash
php bin/hyperf.php elastic:migration --index=users
```

### Aplicar Migrations

```bash
php bin/hyperf.php elastic:migrate
```

## Próximos Passos

Consulte a documentação detalhada para cada componente:

- [ElasticQueryBuilder](ElasticQueryBuilder.md)
- [Migrations](Migrations.md)
- [Commands](Commands.md)
