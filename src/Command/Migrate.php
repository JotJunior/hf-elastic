<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Di\Annotation\Inject;
use Jot\HfElastic\ElasticsearchService;
use Jot\HfElastic\Exception\MissingMigrationDirectoryException;
use Psr\Container\ContainerInterface;

#[Command]
class Migrate extends HyperfCommand
{
    #[Inject]
    protected ElasticsearchService $esClient;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('elastic:migrate');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Elasticsearch migrate mappings command');
    }

    public function handle()
    {
        $migrationDirectory = getcwd() . '/migrations/elasticsearch';

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
                if ($this->es()->indices()->exists(['index' => $index])) {
                    $response = $this->es()->indices()->putMapping([
                        'index' => $index,
                        'body' => $mapping['mappings']
                    ]);
                    $this->line(sprintf('UPDATED %s', $index));
                } else {
                    $response = $this->es()->indices()->create([
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
