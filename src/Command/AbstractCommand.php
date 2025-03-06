<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Hyperf\Command\Command as HyperfCommand;
use Jot\HfElastic\Contracts\CommandInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractCommand extends HyperfCommand implements CommandInterface
{
    /**
     * The migration directory path.
     *
     * @var string
     */
    protected string $migrationDirectory;

    /**
     * AbstractCommand constructor.
     *
     * @param ContainerInterface $container The container instance.
     * @param string $name The name of the command.
     */
    public function __construct(protected ContainerInterface $container, string $name)
    {
        parent::__construct($name);
        
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', \dirname(__DIR__, 4));
        }
        
        $this->migrationDirectory = BASE_PATH . '/migrations/elasticsearch';
    }

    /**
     * Check if the migration directory exists.
     *
     * @return bool True if the directory exists, false otherwise.
     */
    protected function migrationDirectoryExists(): bool
    {
        if (!is_dir($this->migrationDirectory)) {
            $this->line(sprintf('<fg=red>[ERROR]</> Missing migration directory %s', $this->migrationDirectory));
            return false;
        }
        
        return true;
    }

    /**
     * Create the migration directory if it doesn't exist.
     *
     * @return bool True if the directory exists or was created successfully, false otherwise.
     */
    protected function createMigrationDirectoryIfNotExists(): bool
    {
        if (!is_dir($this->migrationDirectory)) {
            return mkdir($this->migrationDirectory, 0755, true);
        }
        
        return true;
    }

    /**
     * Get all migration files.
     *
     * @param string|null $index Filter by index name.
     * @param string|null $migrationFile Filter by migration file name.
     * @return array An array of migration files with their instances.
     */
    protected function getMigrationFiles(?string $index = null, ?string $migrationFile = null): array
    {
        $result = [];
        
        foreach (glob($this->migrationDirectory . '/*.php') as $file) {
            $migration = include $file;
            
            if ($index && $migration::INDEX_NAME !== $index) {
                continue;
            }
            
            if ($migrationFile && $migrationFile !== basename($file)) {
                continue;
            }
            
            $result[$file] = $migration;
        }
        
        return $result;
    }
}
