# jot/hf-elastic

## Descrição

O pacote **jot/hf-elastic** é uma solução completa para integrar o Elasticsearch com aplicações PHP baseadas no framework Hyperf. O objetivo principal é oferecer uma biblioteca que abstrai e facilita a utilização do Elasticsearch, fornecendo uma API fluente para construção de consultas, um sistema de migrations para gerenciar índices e comandos para administração via linha de comando.

## Características

- API fluente para construção de consultas (inspirada no Eloquent)
- Sistema de migrations para gerenciar a estrutura dos índices
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

## Exemplo de Uso

O exemplo abaixo mostra como injetar o serviço em um controller para consultar e entregar os dados de um registro no Elasticsearch:

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Jot\HfElastic\Query\ElasticQueryBuilder;

#[Controller]
class UserController
{
    #[Inject]
    protected ElasticQueryBuilder $queryBuilder;

    #[GetMapping(path: '/users/{id}')]
    public function getUserData(string $id)
    {
        return $this->queryBuilder
            ->from('users')             
            ->where('id', '=', $id)    
            ->execute();                
    }
}
```

## Documentação Detalhada

Para obter informações mais detalhadas sobre como usar o pacote, consulte a documentação completa disponível em:

- [Documentação Principal](docs/index.md): Visão geral e introdução ao pacote
- [ElasticQueryBuilder](docs/ElasticQueryBuilder.md): Guia completo sobre o uso do ElasticQueryBuilder
- [Migrations](docs/Migrations.md): Documentação sobre o sistema de migrations
- [Commands](docs/Commands.md): Referência dos comandos disponíveis
- [Operadores](docs/operators.md): Detalhes sobre os operadores disponíveis para consultas
- [Exemplos Avançados](docs/advanced-examples.md): Exemplos avançados de uso do ElasticQueryBuilder
- [Uso do QueryBuilder](docs/query-builder-usage.md): Guia detalhado sobre o uso do QueryBuilder

---

# QueryBuilder

A QueryBuilder é uma abstração para executar buscas no elasticsearch com formato semelhante ao SQL, inspirado no ORM
Eloquent.

## SELECT

Executa uma busca no Elasticsearch e retorna um array con o resultado.

```php
use Hyperf\Context\ApplicationContext;
use Jot\HfElastic\QueryBuilder;

$queryBuilder = ApplicationContext::getContainer()->get(QueryBuilder::class);

$user = $queryBuilder
    ->select('*')               
    ->from('users')             
    ->where('id',  '=', $id)    
    ->execute();                
```

### Operadores

**comparison**: Operadores básicos para comparação dos dados. Os operadores existentes são =, !=, >, >=, <, <=

```php
$user = $queryBuilder
    ->select('*')               
    ->from('users')             
    ->where('status', '=', 'active')    
    ->where('tags', '!=', 'vip')    
    ->where('birthdate', '>', '1980-01-01')
    ->where('salary', '<=', 2000)
    ->execute();                
```

**between**: Retorna registros com um range de valores. Somente funciona para os tipos numéricos e data.

```php
$user = $queryBuilder
    ->select(['id', 'name'])               
    ->from('users')             
    ->where('birthdate',  'between', ['1980-01-01', '2000-01-01'])    
    ->execute();

$user = $queryBuilder
    ->select(['id', 'name'])               
    ->from('users')             
    ->where('salary',  'between', [1000, 2000])    
    ->execute();
```

**exists**: Retorna o resultado somente se um determinado campo existir na base. Considera-se um campo não existente os
que não existam no documento ou cujo seu valor seja null;

```php
$user = $queryBuilder
    ->select(['id', 'name'])               
    ->from('users')             
    ->where('tags',  'exists')    
    ->execute();
```

**like**: Usa o wildcard para busca de conteúdo dentro de uma string.

```php
$user = $queryBuilder
    ->select(['id', 'name'])               
    ->from('users')             
    ->where('email', 'like' , '%gmail%')    
    ->execute();
```

**prefix**: Semelhante ao wildcard, mas para conteúdos que iniciam com a string buscada. É mais performático que o
wildcard.

```php
$user = $queryBuilder
    ->select(['id', 'name'])               
    ->from('users')             
    ->where('email', 'prefix' , 'maria')    
    ->execute();
```

**distance**: Procura registros que estejam dentro de um raio de distancia a partir da latitude e longitude informados.

```php
$user = $queryBuilder
    ->select(['id', 'name'])               
    ->from('users')             
    ->where('last_location',  'distance', ['lat' => 22.9519326, 'lon' =>-43.2214636, 'distance' => '1km'])    
    ->execute();                
```

## INSERT

Indexa um documento no Elasticsearch.

```php
$queryBuilder
    ->into('users')
    ->create([
        'name' => 'John doe',
        'email' => 'john@doe.com',
        'birth_date' => '1980-01-01',
        'phone_number' => '+5511987651234'
    ]);
```

É possível definir manualmente o ID do registro. Nesse caso, esse ID será verificado e uma exceção será disparada caso o
ID já exista na base. Caso não seja definido o id, este será definido por um UUID.

## UPDATE

Altera os dados de um documento existente.

```php
$queryBuilder
    ->from('users')
    ->update($id, [
        'email' => 'new-john@doe.com',
    ]);
```

### UPDATE BY QUERY

Atualiza diversos registros a partir de uma query:

```php
$disableMultipleUsers = $queryBuilder
    ->select()               
    ->from('users')             
    ->where('email', 'like' , '%hotmail%')    
    ->bulkUpdate(
        data: ['status' => 'disabled']
    );
```

## DELETE

A remoção pode ser tanto lógica quanto física.

**Remoção lógica:**

```php
$queryBuilder
    ->from('users')
    ->delete(id: $id);
```

**Remoção física:**

```php
$queryBuilder
    ->from('users')
    ->delete(
        id: $id, 
        logicalDeletion: true
    );
```

Caso o campo de remoção não seja o padrão da aplicação (deleted), você pode informar qual o campo será marcado como
removido.

```php
$queryBuilder
    ->from('users')
    ->delete(
        id: $id, 
        logicalDeletion: true, 
        field: 'removed_boolean_field'
    );
```

Quando executada uma remoção lógica, os campos updated_at e @version serão atualizados automaticamente.

### DELETE BY QUERY

Remove de forma lógica ou definitiva os documentos encontrados em uma busca.

```php
$disableMultipleUsers = $queryBuilder
    ->select()               
    ->from('users')             
    ->where('email', 'prefix' , 'hotmail')    
    ->bulkDelete(
        logicalDeletion: true, // default true
        field: 'deleted',      // default 'deleted'
    );
```

## Migrations

O pacote dispõe de um sistema de migrations para gerenciar os índices do Elasticsearch. Para criar uma migration, utilize o comando:

```bash
php bin/hyperf.php elastic:migration --index=users
```

Para executar as migrations:

```bash
php bin/hyperf.php elastic:migrate
```

Consulte a [documentação de migrations](docs/Migrations.md) para mais detalhes.

## Comandos Disponíveis

O pacote fornece vários comandos para gerenciar índices do Elasticsearch:

- `elastic:migration`: Cria um novo arquivo de migration para um índice
- `elastic:migrate`: Executa as migrations pendentes
- `elastic:reset`: Remove e recria todos os índices
- `elastic:destroy`: Remove todos os índices

Consulte a [documentação de comandos](docs/Commands.md) para mais detalhes sobre cada comando e suas opções.

## Contribuindo

Contribuições são bem-vindas! Se você encontrar um bug ou tiver uma sugestão de melhoria, sinta-se à vontade para abrir uma issue ou enviar um pull request.

## Licença

Este pacote é open-source e está disponível sob a licença MIT.

## Apoie este projeto ❤️

Se você gostou deste projeto e quer apoiá-lo, considere fazer uma doação! Qualquer valor ajuda a manter este projeto ativo e em contínua evolução.

- **PayPal**: [Doe agora](https://www.paypal.com/donate?business=jot@jot.com.br)  
  *(e-mail do PayPal: **jot@jot.com.br**)*