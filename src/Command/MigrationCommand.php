<?php

declare(strict_types=1);

namespace Jot\HfElastic\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Jot\HfElastic\ElasticsearchService;
use Jot\HfElastic\Exception\MissingMigrationDirectoryException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class MigrationCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('elastic:migration');
        $this->setDescription('Create a new migration for Elasticsearch.');
        $this->addUsage('elastic:migration --index=index_name');
        $this->addUsage('elastic:migration --index=index_name --update');
        $this->addOption('index', 'I', InputOption::VALUE_REQUIRED, 'The index name.');
        $this->addOption('update', 'U', InputOption::VALUE_NONE, 'Update an existing index.');
    }

    public function handle()
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', \dirname(__DIR__, 4));
        }
        $migrationDirectory = BASE_PATH . '/migrations/elasticsearch';

        if (!is_dir($migrationDirectory)) {
            $this->line('<fg=red>[ERROR]</> The migration directory does not exist.');
            return;
        }
        $indexName = $this->input->getOption('index');
        $update = $this->input->getOption('update');
        $template = $update ? $this->updateTemplate($indexName) : $this->createTemplate($indexName);
        $migrationFile = sprintf('%s/%s-%s.php', $migrationDirectory, date('YmdHis'), $indexName);
        file_put_contents($migrationFile, $template);
        $this->line(sprintf('<fg=green>[OK]</> Migration file created at %s', $migrationFile));
        $this->line('     Run <fg=yellow>`php bin/hyperf.php elastic:migrate`</> to apply the migration.');
    }

    private function createTemplate(string $indexName): string
    {
        return <<<PHP
<?php

use Jot\HfElastic\Migration;
use Jot\HfElastic\Migration\Mapping;

return new class extends Migration {

    public const INDEX_NAME = '$indexName';

    public function up(): void
    {
        \$index = new Mapping(name: self::INDEX_NAME);

        \$index->keyword('id');
        \$index->keyword('name')->normalizer('normalizer_ascii_lower');
        \$index->date('created_at');
        \$index->date('updated_at');
        \$index->boolean('removed');

        \$index->settings([
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

        \$this->create(\$index);

    }

    public function down(): void
    {
        \$this->delete();
    }
};
PHP;

    }

    private function updateTemplate(string $indexName): string
    {
        return <<<PHP
<?php

use Jot\HfElastic\Migration;use Jot\HfElastic\Migration\ElasticsearchType\Type;use Jot\HfElastic\Migration\Mapping;

return new class extends Migration {

    private \$indexName = '$indexName';

    public function up(): void
    {
        \$index = new Mapping(index: \$this->indexName);
        \$index->property(field: 'id', type: Type::keyword);
        \$index->property(field: 'name', type: Type::keyword, options: ['normalizer' => 'normalizer_ascii_lower']);
        \$index->property(field: 'created_at', type: Type::date);
        \$index->property(field: 'updated_at', type: Type::date);
        \$index->property(field: 'removed', type: Type::boolean);
        
        \$this->update(\$index);
    }
};<?php

use Jot\HfElastic\Migration;
use Jot\HfElastic\Migration\Mapping;

return new class extends Migration {

    public const INDEX_NAME = 'users';

    public function up(): void
    {
        \$index = new Mapping(name: self::INDEX_NAME);

        /*
         * Add new fields here
         * ex: \$index->keyword('new_field');
         * 
         * *** I M P O R T A N T ***
         * You cannot change the type of an existing field.
         * *** I M P O R T A N T ***
         */

        \$this->update(\$index);

    }
PHP;

    }
}
