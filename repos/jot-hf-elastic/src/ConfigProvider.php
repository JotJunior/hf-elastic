<?php

declare(strict_types=1);

namespace Jot\HfElastic;

use Jot\HfElasticCore\ConfigProvider as CoreConfigProvider;
use Jot\HfElasticQuery\ConfigProvider as QueryConfigProvider;
use Jot\HfElasticMigrations\ConfigProvider as MigrationsConfigProvider;

/**
 * ConfigProvider for the jot/hf-elastic metapackage.
 * This provider aggregates configurations from all subpackages.
 */
class ConfigProvider
{
    /**
     * Return the container definitions.
     */
    public function __invoke(): array
    {
        // Load configurations from subpackages
        $coreConfig = (new CoreConfigProvider())();
        $queryConfig = (new QueryConfigProvider())();
        $migrationsConfig = (new MigrationsConfigProvider())();
        
        // Register class aliases for backward compatibility
        $this->registerClassAliases();
        
        // Merge configurations
        return $this->mergeConfigurations($coreConfig, $queryConfig, $migrationsConfig);
    }
    
    /**
     * Register class aliases for backward compatibility.
     */
    private function registerClassAliases(): void
    {
        // Core interfaces
        class_alias('Jot\\HfElasticCore\\Contracts\\ClientFactoryInterface', 'Jot\\HfElastic\\Contracts\\ClientFactoryInterface');
        class_alias('Jot\\HfElasticCore\\Contracts\\CommandInterface', 'Jot\\HfElastic\\Contracts\\CommandInterface');
        class_alias('Jot\\HfElasticCore\\Contracts\\ElasticRepositoryInterface', 'Jot\\HfElastic\\Contracts\\ElasticRepositoryInterface');
        class_alias('Jot\\HfElasticCore\\Contracts\\MappingGeneratorInterface', 'Jot\\HfElastic\\Contracts\\MappingGeneratorInterface');
        class_alias('Jot\\HfElasticCore\\Contracts\\MappingInterface', 'Jot\\HfElastic\\Contracts\\MappingInterface');
        class_alias('Jot\\HfElasticCore\\Contracts\\MigrationInterface', 'Jot\\HfElastic\\Contracts\\MigrationInterface');
        class_alias('Jot\\HfElasticCore\\Contracts\\OperatorStrategyInterface', 'Jot\\HfElastic\\Contracts\\OperatorStrategyInterface');
        class_alias('Jot\\HfElasticCore\\Contracts\\PropertyInterface', 'Jot\\HfElastic\\Contracts\\PropertyInterface');
        class_alias('Jot\\HfElasticCore\\Contracts\\QueryBuilderInterface', 'Jot\\HfElastic\\Contracts\\QueryBuilderInterface');
        
        // Query classes
        class_alias('Jot\\HfElasticQuery\\Facade\\QueryBuilder', 'Jot\\HfElastic\\Facade\\QueryBuilder');
        class_alias('Jot\\HfElasticQuery\\Query\\ElasticQueryBuilder', 'Jot\\HfElastic\\Query\\ElasticQueryBuilder');
        class_alias('Jot\\HfElasticQuery\\Query\\OperatorRegistry', 'Jot\\HfElastic\\Query\\OperatorRegistry');
        class_alias('Jot\\HfElasticQuery\\Query\\QueryContext', 'Jot\\HfElastic\\Query\\QueryContext');
        class_alias('Jot\\HfElasticQuery\\Query\\Operators\\EqualsOperator', 'Jot\\HfElastic\\Query\\Operators\\EqualsOperator');
        class_alias('Jot\\HfElasticQuery\\Query\\Operators\\NotEqualsOperator', 'Jot\\HfElastic\\Query\\Operators\\NotEqualsOperator');
        class_alias('Jot\\HfElasticQuery\\Query\\Operators\\RangeOperator', 'Jot\\HfElastic\\Query\\Operators\\RangeOperator');
        class_alias('Jot\\HfElasticQuery\\Repository\\ElasticRepository', 'Jot\\HfElastic\\Repository\\ElasticRepository');
        
        // Migrations classes
        class_alias('Jot\\HfElasticMigrations\\Command\\AbstractCommand', 'Jot\\HfElastic\\Command\\AbstractCommand');
        class_alias('Jot\\HfElasticMigrations\\Command\\DestroyCommand', 'Jot\\HfElastic\\Command\\DestroyCommand');
        class_alias('Jot\\HfElasticMigrations\\Command\\MigrateCommand', 'Jot\\HfElastic\\Command\\MigrateCommand');
        class_alias('Jot\\HfElasticMigrations\\Command\\MigrationCommand', 'Jot\\HfElastic\\Command\\MigrationCommand');
        class_alias('Jot\\HfElasticMigrations\\Command\\ResetCommand', 'Jot\\HfElastic\\Command\\ResetCommand');
        class_alias('Jot\\HfElasticMigrations\\Migration', 'Jot\\HfElastic\\Migration');
        class_alias('Jot\\HfElasticMigrations\\Migration\\Mapping', 'Jot\\HfElastic\\Migration\\Mapping');
        class_alias('Jot\\HfElasticMigrations\\Migration\\Property', 'Jot\\HfElastic\\Migration\\Property');
    }
    
    /**
     * Merge configurations from all subpackages.
     */
    private function mergeConfigurations(array $coreConfig, array $queryConfig, array $migrationsConfig): array
    {
        $result = $coreConfig;
        
        // Merge dependencies
        if (isset($queryConfig['dependencies'])) {
            $result['dependencies'] = array_merge(
                $result['dependencies'] ?? [],
                $queryConfig['dependencies']
            );
        }
        
        if (isset($migrationsConfig['dependencies'])) {
            $result['dependencies'] = array_merge(
                $result['dependencies'] ?? [],
                $migrationsConfig['dependencies']
            );
        }
        
        // Merge commands
        if (isset($queryConfig['commands'])) {
            $result['commands'] = array_merge(
                $result['commands'] ?? [],
                $queryConfig['commands']
            );
        }
        
        if (isset($migrationsConfig['commands'])) {
            $result['commands'] = array_merge(
                $result['commands'] ?? [],
                $migrationsConfig['commands']
            );
        }
        
        // Merge annotations paths
        if (isset($queryConfig['annotations']['scan']['paths'])) {
            $result['annotations']['scan']['paths'] = array_merge(
                $result['annotations']['scan']['paths'] ?? [],
                $queryConfig['annotations']['scan']['paths']
            );
        }
        
        if (isset($migrationsConfig['annotations']['scan']['paths'])) {
            $result['annotations']['scan']['paths'] = array_merge(
                $result['annotations']['scan']['paths'] ?? [],
                $migrationsConfig['annotations']['scan']['paths']
            );
        }
        
        // Merge publish configurations
        if (isset($queryConfig['publish'])) {
            $result['publish'] = array_merge(
                $result['publish'] ?? [],
                $queryConfig['publish']
            );
        }
        
        if (isset($migrationsConfig['publish'])) {
            $result['publish'] = array_merge(
                $result['publish'] ?? [],
                $migrationsConfig['publish']
            );
        }
        
        return $result;
    }
}
