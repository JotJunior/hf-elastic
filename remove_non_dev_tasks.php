<?php

// Ler o arquivo JSON
$jsonFilePath = __DIR__ . '/split_package_roadmap.json';
$jsonContent = file_get_contents($jsonFilePath);
$data = json_decode($jsonContent, true);

// Palavras-chave que indicam tarefas nu00e3o relacionadas diretamente ao desenvolvimento
$nonDevKeywords = [
    'publicar', 'anunciar', 'avisar', 'lanu00e7amento', 'feedback', 'coletar',
    'comunidade', 'monitorar', 'suporte', 'comunicar', 'anuncio', 'anu00fancio',
    'release notes', 'notificar', 'comunicado', 'divulgar', 'promover',
    'packagist', 'alpha', 'beta', 'estu00e1vel', 'versu00e3o', 'convidar', 'usuu00e1rios'
];

// Funu00e7u00e3o para verificar se uma tarefa u00e9 de desenvolvimento
function isDevTask($taskDescription) {
    global $nonDevKeywords;
    
    $taskDescription = mb_strtolower($taskDescription);
    
    foreach ($nonDevKeywords as $keyword) {
        if (mb_strpos($taskDescription, mb_strtolower($keyword)) !== false) {
            return false;
        }
    }
    
    return true;
}

// Funu00e7u00e3o recursiva para filtrar tarefas nu00e3o relacionadas ao desenvolvimento
function filterNonDevTasks(&$item) {
    if (is_array($item)) {
        if (isset($item['tasks']) && is_array($item['tasks'])) {
            $filteredTasks = [];
            foreach ($item['tasks'] as $task) {
                if (isset($task['description'])) {
                    if (isDevTask($task['description'])) {
                        $filteredTasks[] = $task;
                    }
                } else {
                    // Se nu00e3o tiver descriu00e7u00e3o, manter (caso raro)
                    $filteredTasks[] = $task;
                }
            }
            $item['tasks'] = $filteredTasks;
        }
        
        // Processar recursivamente todos os arrays
        foreach ($item as $key => &$value) {
            if (is_array($value)) {
                filterNonDevTasks($value);
            }
        }
    }
}

// Filtrar tarefas nu00e3o relacionadas ao desenvolvimento
filterNonDevTasks($data);

// Verificar e remover etapas que ficaram sem tarefas
foreach ($data['phases'] as $phaseKey => &$phase) {
    foreach ($phase['steps'] as $stepKey => &$step) {
        if (empty($step['tasks'])) {
            // Remover etapa sem tarefas
            unset($phase['steps'][$stepKey]);
        }
    }
    
    // Reindexar o array de etapas
    $phase['steps'] = array_values($phase['steps']);
    
    // Se a fase ficou sem etapas, removu00ea-la
    if (empty($phase['steps'])) {
        unset($data['phases'][$phaseKey]);
    }
}

// Reindexar o array de fases
$data['phases'] = array_values($data['phases']);

// Salvar o arquivo atualizado
$updatedJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents($jsonFilePath, $updatedJson);

echo "Arquivo JSON atualizado com sucesso! Tarefas nu00e3o relacionadas ao desenvolvimento foram removidas.\n";
