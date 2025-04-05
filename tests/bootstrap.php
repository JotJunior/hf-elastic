<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

require BASE_PATH . '/vendor/autoload.php';


// Configurar o container para os testes
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Di\Container;
use Hyperf\Engine\DefaultOption;
use Mockery as m;

! defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', DefaultOption::hookFlags());

// Create a mock for the __() function in the global namespace
if (! function_exists('__')) {
    function __($key, array $replace = [], ?string $locale = null)
    {
        // Mapping of translation keys to their expected values in tests
        $translations = [
            // Error messages
            'messages.hf_elastic.invalid_file' => '{file} is not a valid file or url.',
            'messages.hf_elastic.unreadable_file' => 'Could not read file: {file}',
            'messages.hf_elastic.invalid_template_format' => 'Invalid template format',
            'messages.hf_elastic.document_not_found' => 'Document not found',
            'messages.hf_elastic.error_occurred' => '{message}',
            'messages.hf_elastic.unsupported_operator' => 'Unsupported operator: {operator}',
            'messages.hf_elastic.invalid_query' => 'Invalid query',
            'messages.hf_elastic.invalid_field' => '{field} is not valid',

            // Direct keys without messages. prefix
            'hf-elastic.document_not_found' => 'Document not found',
            'hf-elastic.error_occurred' => '{message}',
            'hf-elastic.invalid_file' => '/non/existent/file.json is not a valid file or url.',
            'hf-elastic.no_migration' => '[info] No migrations found',
            'hf-elastic.console_migration_directory_failed' => '[error] Failed to create migration directory',
        ];

        // Get the translated message or use the key as fallback
        $message = $translations[$key] ?? $key;

        // Replace placeholders with values
        if (! empty($replace)) {
            foreach ($replace as $placeholder => $value) {
                $message = str_replace('{' . $placeholder . '}', $value, $message);
            }
        }

        return $message;
    }
}

// Set the container in ApplicationContext
if (! ApplicationContext::hasContainer()) {
    $container = m::mock(Container::class);
    $container->shouldReceive('get')->with(TranslatorInterface::class)->andReturnUsing(function () {
        $translator = m::mock(TranslatorInterface::class);
        $translator->shouldReceive('trans')->andReturnUsing(function ($key, $replace = [], $locale = null) {
            // Use the __() function we defined above to translate messages
            return __($key, $replace, $locale);
        });
        return $translator;
    });

    ApplicationContext::setContainer($container);
}
