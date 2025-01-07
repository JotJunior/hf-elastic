# hf-elastic

## Descrição

O projeto **hf-elastic** é uma implementação que integra o uso do Elasticsearch utilizando o framework Hyperf e seu
pacote oficial do elasticsearch. O objetivo principal é oferecer uma biblioteca que abstrai e facilita a utilização do
Elasticsearch.

## Requisitos

- **PHP**: 8.1 ou superior
- **Elasticsearch**: 7.x
- **Composer**: Para gerenciar as dependências do projeto

## Instalando a biblioteca

1. Requisitando por composer:
   ```bash
   composer require jot/hf-elastic
   ```

2. Certifique-se de que você possua uma instância do Elasticsearch configurada e rodando.

3. Certifique-se de que você possua uma instância do ETCD configurada e rodando.

## Adicionando credenciais no ETCD

```bash
etcdctl put '/services/elasticsearch/host' 'https://127.0.0.1:9200'
etcdctl put '/services/elasticsearch/username' 'elastic'
etcdctl put '/services/elasticsearch/password' 'es-password'
```

## Dependências Principais

Este projeto faz uso das dependências principais abaixo:

- **guzzlehttp/guzzle** (v7.9.2): Cliente HTTP assíncrono para lidar com requisições.
- **hyperf/elasticsearch** (v3.1.42): Conectividade e manuseio do Elasticsearch.
- **symfony/console** (v7.2.1): Facilita a criação de comandos de CLI.
- **hyperf/coroutine** (v3.1.49): Suporte para co-rotinas no Hyperf.
- **doctrine/inflector** (v2.0.10): Manipulação de strings de forma inteligente.

Para uma lista completa das dependências, confira o arquivo `composer.json`.

## Utilizando a biblioteca no seu código

A biblioteca já está preparada para uso sem maiores configurações além de adicionar as credenciais no ETCD, bastando
injeta-la no código na construtora ou via annotation ```#[Inject]```.

### Exemplo de uso

```php
<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Jot\HfElastic\ElasticsearchService;

#[Controller]
class UserController
{
    #[Inject]
    protected ElasticsearchService $esService;

    #[GetMapping(path: '/users/{id}')]
    public function getUserData(string $id)
    {
        return $this->esService->get(id: $id, index: 'users');
    }

}
```

### Principais métodos

Foi criado para esta biblioteca alguns métodos para facilitar a extração e indexação dos dados

```php
/** 
 * @return array|callable
 */
$this->esService->get(id: $id, index: $index); 

/** 
 * @return bool 
 */
$this->esService->exists(id: $id, index: $index); // bool

/** 
 * @return bool 
 */
$this->esService->delete(id: $id, index: $index); // bool

/** 
 * @return array|callable 
 */
$this->esService->insert(body: $docBody, id: $id, index: $index); // array|callable

/** 
 * OBSERVAÇÃO
 * Indexar é muito mais performático que alterar um registro. 
 * Este método carrega o registro original, faz um merge do 
 * conteúdo enviado e indexa novamente.
 * 
 * @return array|callable 
 */
$this->esService->update(body: $docBody, id: $id, index: $index); // array|callable

/**
 * Retorna o client do elasticsearch instanciado
 * 
 * @return \Elasticsearch\Client
 */
$this->esService->es(); 

```

## Comandos disponíveis

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
jot@macbook-jot auth %
```

### elastic:migration | Criando uma migration

O comando abaixo vai gerar um arquivo de migrations com configurações básicas para criação do índice. O comando tem o
parâmetro ```--index=``` para definir o nome do índice a ser criado e o comando opcional ```--update```  para quando a
migration vai adicionar novos campos a um índice existente.

```shell
php bin/hyperf.php elastic:migration --index=nome_do_indice [--update]
```

Esse comando cria um arquivo de migration no diretório migrations/elasticsearch a partir da raiz do projeto

```plaintext
seu-projeto/
├── migrations/                              
├──── elasticsearch/                         
├────── YYYYmmddHHiiss-nome_do_indice.php    
```

O resultado final será um arquivo com os campos básicos de id, name, datas de criação e um campo para remoção lógica.
Com o arquivo criado, basta editá-lo adicionando os campos desejados.

```php
<?php

use Jot\HfElastic\Migration;
use Jot\HfElastic\Migration\Mapping;
use Jot\HfElastic\Migration\ElasticsearchType\Child;
use Jot\HfElastic\Migration\ElasticsearchType\Nested;

return new class extends Migration {

    public const INDEX_NAME = 'users';

    public function up(): void
    {
        // Iniciando a criação do mapping do índice
        $index = new Mapping(name: self::INDEX_NAME);

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
        
        // criando o objeto simples para o endereço do usuário
        $address = new Child(name: 'address');
        $address->keyword(name: 'street');
        $address->keyword(name: 'number');
        // criando o objeto da cidade e vinculando ao endereço                
        $city = new Child(name: 'city');
        $city->keyword(name: 'id');
        $city->keyword(name: 'name');
        $address->child(child: $city);
        // vinculando o endereço ao usuário        
        $index->child(child: $address);
                
        // criando um objeto nested
        $logins = new Nested(name: 'last_logins');
        $logins->keyword(name: 'user_agent');
        $logins->ip(name: 'ip_address');
        $logins->date(name: 'datetime');
        // vinculando o objeto ao usuário 
        $index->nested(nested: $nestedObject);
                 
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

### elastic:migrate | Executando as migrações

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

### elastic:reset | Excluindo e recriando seus índices

Este comando vai voltar seus índices para o estado inicial, removendo e recriando com as configurações contidas nas
migrações. Você será questionado se tem certeza de que deseja remover os índices configurados nas migrações.

```shell
$ php bin/hyperf.php elastic:reset
WARNING :: WARNING :: WARNING
This command will remove and re-create all indices. The operation cannot be undone and all data will be lost.

Are you sure you want to remove all indices? [y/N] [N]:
```

### elastic:drop | Excluindo definitivamente seus índices

Este comando vai remover todos os índices que estão configurados nas migrações. Você será questionado da ação,
informando que a ação é irreversível e que todos os dados nos índices relacionaods serão perdidos.

```shell
php elastic:destroy
WARNING :: WARNING :: WARNING
This command will remove all indices. The operation cannot be undone and all data will be lost.

Are you sure you want to remove all indices? [y/N] [N]:
```

## Contribuição

Contribuições são bem-vindas! Sinta-se livre para abrir issues ou pull requests. Antes de contribuir, por favor, siga as
regras abaixo:

1. Faça um fork do projeto.
2. Crie uma branch para a sua funcionalidade/bug fix:
   ```bash
   git checkout -b minha-funcionalidade
   ```
3. Submeta um Pull Request no repositório principal.

## Apoie este projeto ❤️

Se você gostou deste projeto e quer apoiá-lo, considere fazer uma doação! Qualquer valor ajuda a manter este projeto
ativo e em contínua evolução.

### Doações via PayPal

Você pode realizar uma doação através do PayPal usando o seguinte link ou endereço:

- **PayPal**: [Doe agora](https://www.paypal.com/donate?business=jot@jot.com.br)  
  *(e-mail do PayPal: **jot@jot.com.br**)*