<?php

// Ler o arquivo JSON
$jsonFilePath = __DIR__ . '/split_package_roadmap.json';
$jsonContent = file_get_contents($jsonFilePath);
$data = json_decode($jsonContent, true);

// Função recursiva para atualizar as tarefas
function updateTasks(&$item) {
    if (is_array($item)) {
        if (isset($item['tasks']) && is_array($item['tasks'])) {
            $updatedTasks = [];
            foreach ($item['tasks'] as $task) {
                if (is_string($task)) {
                    // Converter string para objeto com status
                    $updatedTasks[] = [
                        'description' => $task,
                        'status' => 'pending'
                    ];
                } else {
                    // Já é um objeto, apenas garantir que tenha status
                    if (!isset($task['status'])) {
                        $task['status'] = 'pending';
                    }
                    $updatedTasks[] = $task;
                }
            }
            $item['tasks'] = $updatedTasks;
        }
        
        // Processar recursivamente todos os arrays
        foreach ($item as $key => &$value) {
            if (is_array($value)) {
                updateTasks($value);
            }
        }
    }
}

// Atualizar todas as tarefas no JSON
updateTasks($data);

// Salvar o arquivo atualizado
$updatedJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents($jsonFilePath, $updatedJson);

echo "Arquivo JSON atualizado com sucesso!\n";
