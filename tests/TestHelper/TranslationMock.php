<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\TestHelper;

/**
 * Mock function for Hyperf\Translation\__
 * This function is used to mock translations in tests.
 * @param mixed $key
 */
function __($key, array $replace = [])
{
    // Map of translation keys to their expected values in tests
    $translations = [
        'hf-elastic.document_not_found' => 'Document not found',
        'hf-elastic.error_occurred' => isset($replace['message']) ? $replace['message'] : 'Error occurred',
        'hf-elastic.no_migration' => '[info] No migrations found',
        'hf-elastic.console_migration_directory_failed' => '[error] Failed to create migration directory',
        'hf-elastic.invalid_file' => '/non/existent/file.json is not a valid file or url.',
    ];

    return $translations[$key] ?? $key;
}
