# Diagrama de Dependu00eancias

```
+----------------+       +-------------------+      +----------------+
|                |       |                   |      |                |
|     CORE       |<------|   QUERY BUILDER   |      |   MIGRATIONS   |
|                |       |                   |      |                |
+----------------+       +-------------------+      +----------------+
        ^                         ^                        ^
        |                         |                        |
        |                         |                        |
        +--------------------------+------------------------+
                                  |
                                  |
                          +----------------+
                          |                |
                          |   HYPERF 3.1   |
                          |                |
                          +----------------+
```

## Estrutura de Dependu00eancias

### Core
- Depende apenas do framework HyperF 3.1
- Contu00e9m interfaces e classes base
- Fornece funcionalidades compartilhadas

### QueryBuilder
- Depende do Core para interfaces e serviu00e7os bu00e1sicos
- Depende do framework HyperF 3.1
- Fornece funcionalidades de construu00e7u00e3o de consultas

### Migrations
- Depende do Core para interfaces e serviu00e7os bu00e1sicos
- Depende do framework HyperF 3.1
- Fornece funcionalidades de migrau00e7u00e3o de u00edndices

## Observau00e7u00f5es

1. O pacote Core seru00e1 a base para os outros pacotes
2. QueryBuilder e Migrations su00e3o independentes entre si
3. Todos os pacotes dependem do framework HyperF 3.1
