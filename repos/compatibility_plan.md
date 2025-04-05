# Plano de Compatibilidade

## Estratu00e9gia de Versionamento

Todos os pacotes seguiru00e3o o versionamento semu00e2ntico (SemVer):

- **Major (X.0.0)**: Mudanu00e7as incompatu00edveis com versu00f5es anteriores
- **Minor (0.X.0)**: Novas funcionalidades de forma compatu00edvel
- **Patch (0.0.X)**: Correu00e7u00f5es de bugs de forma compatu00edvel

### Versu00f5es Iniciais

- **jot/hf-elastic-core**: 1.0.0
- **jot/hf-elastic-query**: 1.0.0
- **jot/hf-elastic-migrations**: 1.0.0
- **jot/hf-elastic** (metapacote): 1.0.0

## Estratu00e9gia de Compatibilidade

### Metapacote

O pacote original `jot/hf-elastic` seru00e1 convertido em um metapacote que depende dos tru00eas novos pacotes. Isso permitiru00e1 que os projetos existentes continuem funcionando sem alterau00e7u00f5es no cu00f3digo.

### Aliases de Namespace

Para manter a compatibilidade com cu00f3digo existente, o metapacote incluiru00e1 aliases para os namespaces antigos:

```php
// No ConfigProvider do metapacote
class_alias('Jot\\HfElasticCore\\Contracts\\QueryBuilderInterface', 'Jot\\HfElastic\\Contracts\\QueryBuilderInterface');
// ... outros aliases
```

### Possu00edveis Breaking Changes

1. **Mudanu00e7as em Interfaces**: As interfaces seru00e3o movidas para o pacote Core, o que pode causar problemas se o cu00f3digo cliente depender da localizau00e7u00e3o exata dos arquivos.

2. **Dependu00eancias Injetadas**: Projetos que usam injeu00e7u00e3o de dependu00eancia diretamente nas classes podem precisar de ajustes.

3. **Autoloading**: Mudanu00e7as nos namespaces podem afetar o autoloading em alguns casos.

### Mitigau00e7u00e3o de Breaking Changes

1. **Documentau00e7u00e3o Clara**: Fornecer documentau00e7u00e3o detalhada sobre as mudanu00e7as e como migrar.

2. **Aliases de Classe**: Usar aliases de classe para manter compatibilidade com cu00f3digo existente.

3. **Metapacote Abrangente**: Garantir que o metapacote inclua todas as funcionalidades do pacote original.

## Peru00edodo de Suporte

### Pacote Original

O pacote original `jot/hf-elastic` (agora um metapacote) seru00e1 mantido por pelo menos 12 meses apu00f3s o lanu00e7amento dos novos pacotes, com as seguintes fases:

1. **Fase de Transiu00e7u00e3o (0-6 meses)**: Suporte total, incluindo novas funcionalidades.

2. **Fase de Manutenu00e7u00e3o (6-12 meses)**: Apenas correu00e7u00f5es de bugs e atualizau00e7u00f5es de seguranu00e7a.

3. **Fase de Descontinuau00e7u00e3o (apu00f3s 12 meses)**: Apenas atualizau00e7u00f5es cru00edticas de seguranu00e7a.

### Novos Pacotes

Os novos pacotes seguiru00e3o o ciclo normal de desenvolvimento, com suporte de longo prazo para versu00f5es major.

## Guia de Migrau00e7u00e3o

### Para Usuu00e1rios do Pacote Original

1. **Sem Mudanu00e7as Imediatas**: Continuar usando `jot/hf-elastic` sem alterau00e7u00f5es no cu00f3digo.

2. **Migrau00e7u00e3o Gradual**: Atualizar gradualmente as referu00eancias de namespace para os novos pacotes.

### Para Novos Projetos

1. **Abordagem Modular**: Usar apenas os pacotes necessu00e1rios para o projeto.

2. **Dependu00eancias Explu00edcitas**: Declarar dependu00eancias explu00edcitas para os pacotes especu00edficos.
