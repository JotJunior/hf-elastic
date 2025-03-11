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

## Contribuindo

Contribuições são bem-vindas! Se você encontrar um bug ou tiver uma sugestão de melhoria, sinta-se à vontade para abrir uma issue ou enviar um pull request.

## Licença

Este pacote é open-source e está disponível sob a licença MIT.

## Apoie este projeto ❤️

Se você gostou deste projeto e quer apoiá-lo, considere fazer uma doação! Qualquer valor ajuda a manter este projeto ativo e em contínua evolução.

- **PayPal**: [Doe agora](https://www.paypal.com/donate?business=jot@jot.com.br)  
  *(e-mail do PayPal: **jot@jot.com.br**)*