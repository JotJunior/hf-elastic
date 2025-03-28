<?php

declare(strict_types=1);

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL);
date_default_timezone_set('America/Sao_Paulo');

!defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

require BASE_PATH . '/vendor/autoload.php';

! defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', Hyperf\Engine\DefaultOption::hookFlags());

// Configurar o container para os testes
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSource;
use Hyperf\Translation\ConfigProvider;
use Hyperf\Translation\Translator;
use Hyperf\Translation\FileLoader;
use Mockery as m;

// Criar um mock para a funu00e7u00e3o __()
if (!function_exists('Hyperf\Translation\_')) {
    function __($key, array $replace = [], ?string $locale = null)
    {
        // Mapeamento de chaves para mensagens traduzidas
        $translations = [
            'messages.hf_elastic.invalid_file' => '{file} is not a valid file or url.',
            'messages.hf_elastic.unreadable_file' => 'Could not read file: {file}',
            'messages.hf_elastic.invalid_template_format' => 'Invalid template format',
            'messages.hf_elastic.document_not_found' => 'Document not found',
            'messages.hf_elastic.error_occurred' => '{message}',
            'messages.hf_elastic.unsupported_operator' => 'Unsupported operator: {operator}',
            'messages.hf_elastic.invalid_query' => 'Invalid query',
            'messages.hf_elastic.invalid_field' => '{field} is not valid'
        ];
        
        // Obter a mensagem traduzida ou usar a chave como fallback
        $message = $translations[$key] ?? $key;
        
        // Substituir os placeholders pelos valores
        if (!empty($replace)) {
            foreach ($replace as $placeholder => $value) {
                $message = str_replace('{'.$placeholder.'}', $value, $message);
            }
        }
        
        return $message;
    }
}

// Definir o container no ApplicationContext
if (!ApplicationContext::hasContainer()) {
    $container = m::mock(Container::class);
    $container->shouldReceive('get')->with(TranslatorInterface::class)->andReturnUsing(function() {
        $translator = m::mock(TranslatorInterface::class);
        $translator->shouldReceive('trans')->andReturnUsing(function($key, $replace = [], $locale = null) {
            // Usar a funu00e7u00e3o __() que definimos acima para traduzir as mensagens
            return __($key, $replace, $locale);
        });
        return $translator;
    });
    
    ApplicationContext::setContainer($container);
}

