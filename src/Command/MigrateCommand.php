<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Jot\HfElastic\ElasticsearchService;
use Jot\HfElastic\Exception\MissingMigrationDirectoryException;
use Psr\Container\ContainerInterface;

#[Command]
class MigrateCommand extends HyperfCommand
{
    protected ElasticsearchService $esClient;

    public function __construct(protected ContainerInterface $container, ElasticsearchService $esClient)
    {
        parent::__construct('jot:migrate-mappings');
        $this->esClient = $esClient;
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Elasticsearch mappings migrations command.');
    }

    /**
     * Handles the migration of Elasticsearch index mappings.
     *
     * This method scans a predefined migration directory for JSON files containing
     * index mappings. For each valid mapping file, it checks if the corresponding
     * Elasticsearch index exists and either updates its mapping or creates a new index.
     *
     * @return void
     * @throws MissingMigrationDirectoryException If the migration directory is not found.
     */
    public function handle()
    {

        if (!defined('BASE_PATH')) {
            define('BASE_PATH', \dirname(__DIR__, 4));
        }
        $migrationDirectory = BASE_PATH . '/migrations/elasticsearch';

        if (!is_dir($migrationDirectory)) {
            throw new MissingMigrationDirectoryException('Migration directory not found.');
        }

        foreach (glob($migrationDirectory . '/*.json') as $file) {
            $mapping = \json_decode(\file_get_contents($file), true);
            if (!$mapping) {
                continue;
            }

            $af = \explode('/', $file);
            if (!\count($af)) {
                continue;
            }
            $index = \str_replace('.json', '', \array_pop($af));

            try {
                if ($this->esClient->es()->indices()->exists(['index' => $index])) {
                    $response = $this->esClient->es()->indices()->putMapping([
                        'index' => $index,
                        'body' => $mapping['mappings']
                    ]);
                    $this->line(sprintf('UPDATED %s', $index));
                } else {
                    $response = $this->esClient->es()->indices()->create([
                        'index' => $index,
                        'body' => $mapping
                    ]);
                    $this->line(sprintf('CREATED %s', $index));
                }
            } catch (\Exception $e) {
                $this->line(sprintf('ERROR %s', $e->getMessage()));
            }
        }
    }
}
