# hf-elastic

## Descrição

O projeto **hf-elastic** é uma implementação que integra o uso do Elasticsearch utilizando o framework Hyperf e seu
pacote oficial do elasticsearch. O objetivo principal é oferecer uma biblioteca que abstrai e facilita a utilização do
Elasticsearch.

## Instalando a biblioteca

1. Requisitando por composer:
   ```bash
   composer require jot/hf-elastic
   ```

2. Certifique-se de que você possua uma instância do Elasticsearch configurada e rodando.

## Utilizando a biblioteca no seu código

A biblioteca já está preparada para uso sem maiores configurações além de adicionar as credenciais no ETCD, bastando
injeta-la no código na construtora ou via annotation ```#[Inject]```.

### Exemplo de uso

O exemplo abaixo mostra como injetar o serviço em um controller para consultar e entregar os dados de um registro no
Elasticsearch.

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Jot\HfElastic\QueryBuilder;

#[Controller]
class UserController
{
    #[Inject]
    protected QueryBuilder $queryBuilder;

    #[GetMapping(path: '/users/{id}')]
    public function getUserData(string $id)
    {
        return $this->queryBuilder
            ->select('*')               
            ->from('users')             
            ->where('id',  '=', $id)    
            ->execute();                
    }

}
```

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

---

# Comandos disponíveis

Para ver a lista dos comandos disponíveis deste pacote, basta executar o comando abaixo para filtrar os comandos pelo
namespace do elastic:

```shell
$ php bin/hyperf.php elastic
Console Tool
...
Available commands for the "elastic" namespace:
  elastic:destroy    Remove all indices.
  elastic:migrate    Create elasticsearch indices from migrations.
  elastic:migration  Create a new migration for Elasticsearch.
  elastic:reset      Remove and create all indices.
```

## elastic:migration | Criando uma migration

O comando abaixo vai gerar um arquivo de migrations com configurações básicas para criação do índice. O comando tem o
argumento ```index``` para definir o nome do índice a ser criado e o comando opcional ```--update```  para quando a
migration vai adicionar novos campos a um índice existente.

```shell
php bin/hyperf.php elastic:migration nome_do_indice [--update]
```

Esse comando cria um arquivo de migration no diretório migrations/elasticsearch a partir da raiz do projeto

```plaintext
seu-projeto/
├── migrations/                              
├──── elasticsearch/                         
├────── YYYYmmddHHiiss-create-nome_do_indice.php    
├────── YYYYmmddHHiiss-update-nome_do_indice.php    
```

O resultado final será um arquivo com os campos básicos de id, name, e campos default.
Com o arquivo criado, basta editá-lo adicionando os campos desejados.

```php
<?php

use Jot\HfElastic\Migration;
use Jot\HfElastic\Migration\Mapping;
use Jot\HfElastic\Migration\ElasticType\ObjectType;
use Jot\HfElastic\Migration\ElasticType\NestedType;

return new class extends Migration {

    public const INDEX_NAME = 'users';

    public function up(): void
    {
        // Iniciando a criação do mapping do índice
        $index = new Mapping(name: self::INDEX_NAME);

        // campos default (created_at, updated_at, deleted, @timestamp, @version)
        $index->defaults();

        // campos simples
        $index->keyword(name: 'id');
        $index->text(name: 'description');
        $index->date(name: 'created_at');
        $index->date(name: 'updated_at');
        $index->boolean(name: 'removed');
        $index->ip(name: 'last_ip_address');
        $index->geoPoint(name: 'last_location');
        $index->float(name: 'salary');
        $index->integer(name: 'task_counter');
        
        // vinculando um normalizer (definido nas settings) a um campo keyword
        $index->keyword('name')->normalizer('normalizer_ascii_lower');
        
        // criando o objeto simples para o endereço do usário
        $address = new ObjectType(name: 'address');
        $address->keyword(name: 'street');
        $address->keyword(name: 'number');
        // criando o objeto da cidade e vinculando ao endereço                
        $city = new ObjectType(name: 'city');
        $city->keyword(name: 'id');
        $city->keyword(name: 'name');
        $address->child(child: $city);
        // vinculando o endereço ao usário        
        $index->child(child: $address);
                
        // criando um objeto nested
        $logins = new NestedType(name: 'last_logins');
        $logins->keyword(name: 'user_agent');
        $logins->ip(name: 'ip_address');
        $logins->date(name: 'datetime');
        // vinculando o objeto ao usário 
        $index->nested(nested: $logins);
                 
        // configurações do índices
        $index->settings([
            'index' => [
                'number_of_shards' => 3,
                'number_of_replicas' => 1,
            ],
            "analysis" => [
                "normalizer" => [
                    "normalizer_ascii_lower" => [
                        "type" => "custom",
                        "char_filter" => [],
                        "filter" => [
                            "asciifolding",
                            "lowercase"
                        ]
                    ]
                ]
            ]
        ]);

        // criando efetivamente o índice no servidor do Elasticsearch
        $this->create($index);

    }

    public function down(): void
    {
        // removendo completeamente o índice
        $this->delete(indexName: self::INDEX_NAME);
        
    }
};
```

### Criação de migration a partir de JSON ou JSON Schema

É possível criar um arquivo de migration com a configuração básica de todas as propriedades de um JSON ou JSON Schema
usando os comandos abaixo:

#### JSON em arquivo ou URL válida
```shell
php bin/hyperf.php elastic:migration nome_do_indice --json=/path/to/file.json
```
```shell
php bin/hyperf.php elastic:migration nome_do_indice --json=https://example.com/json/content.json
```

#### JSON Schema em arquivo ou URL válida
```shell
php bin/hyperf.php elastic:migration nome_do_indice --json-schema=/path/to/shema.json
```
```shell
php bin/hyperf.php elastic:migration nome_do_indice --json-schema=https://example.com/shema/content.json
```

## elastic:migrate | Executando as migrações

Um fator importante relacionado ao elasticsearch é que após um índice ser criado, não é mais possível remover ou alterar
os tipos de dados dos campos já existentes. Para que isso aconteça, é necessário clonar o índice, criar um novo com os
campos corretos e [reindexá-lo](https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-reindex.html).

Sempre que o ```elastic:migrate``` for executado, ele vai verificar se o índice já existe. Caso exista, a migração será
ignorada.

```shell
$ php bin/hyperf.php elastic:migrate

[SKIP] Index vehicles already exists.
[OK] Index users created.
```

## elastic:reset | Excluindo e recriando seus índices

Este comando vai voltar seus índices para o estado inicial, removendo e recriando com as configurações contidas nas
migrações. Você será questionado se tem certeza de que deseja remover os índices configurados nas migrações.

```shell
$ php bin/hyperf.php elastic:reset
WARNING :: WARNING :: WARNING
This command will remove and re-create all indices. The operation cannot be undone and all data will be lost.

Are you sure you want to remove all indices? [y/N] [N]:
```

## elastic:destroy | Excluindo definitivamente seus índices

Este comando vai remover todos os índices que estão configurados nas migrações. Você será questionado da ação,
informando que a ação é irreversível e que todos os dados nos índices relacionaods serão perdidos.

```shell
php elastic:destroy
WARNING :: WARNING :: WARNING
This command will remove all indices. The operation cannot be undone and all data will be lost.

Are you sure you want to remove all indices? [y/N] [N]:
```

# Apoie este projeto ❤️

Se você gostou deste projeto e quer apoiá-lo, considere fazer uma doação! Qualquer valor ajuda a manter este projeto
ativo e em contínua evolução.

## Doações via PayPal

Você pode realizar uma doação através do PayPal usando o seguinte link ou endereço:

- **PayPal**: [Doe agora](https://www.paypal.com/donate?business=jot@jot.com.br)  
  *(e-mail do PayPal: **jot@jot.com.br**)*