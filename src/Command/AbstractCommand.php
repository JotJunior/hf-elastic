<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Command;

use Hyperf\Command\Command as HyperfCommand;
use Jot\HfElastic\Contracts\CommandInterface;
use Psr\Container\ContainerInterface;

use function Hyperf\Translation\__;

abstract class AbstractCommand extends HyperfCommand implements CommandInterface
{
    /**
     * The migration directory path.
     */
    protected string $migrationDirectory;

    /**
     * AbstractCommand constructor.
     * @param ContainerInterface $container the container instance
     * @param string $name the name of the command
     */
    public function __construct(protected ContainerInterface $container, string $name)
    {
        parent::__construct($name);

        if (! defined('BASE_PATH')) {
            define('BASE_PATH', \dirname(__DIR__, 4));
        }

        $this->migrationDirectory = BASE_PATH . '/migrations/elasticsearch';
    }

    /**
     * Check if the migration directory exists.
     * @return bool true if the directory exists, false otherwise
     */
    protected function migrationDirectoryExists(): bool
    {
        if (! is_dir($this->migrationDirectory)) {
            $this->line(__('hf-elastic.console_missing_directory', ['dir' => $this->migrationDirectory]));
            return false;
        }

        return true;
    }

    /**
     * Create the migration directory if it doesn't exist.
     * @return bool true if the directory exists or was created successfully, false otherwise
     */
    protected function createMigrationDirectoryIfNotExists(): bool
    {
        if (! is_dir($this->migrationDirectory)) {
            return mkdir($this->migrationDirectory, 0755, true);
        }

        return true;
    }

    /**
     * Get all migration files.
     * @param null|string $index filter by index name
     * @param null|string $migrationFile filter by migration file name
     * @return array an array of migration files with their instances
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
