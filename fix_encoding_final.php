<?php

declare(strict_types=1);

// Carregar o arquivo JSON
$roadmapFile = '/Users/jot/Projects/Aevum/libs/hf-elastic/split_package_roadmap.json';
$roadmap = json_decode(file_get_contents($roadmapFile), true);

// Corrigir a codificau00e7u00e3o das tarefas da etapa 2.5
foreach ($roadmap['phases'] as &$phase) {
    if ($phase['id'] == 2) { // Fase 2: Preparau00e7u00e3o da Base de Cu00f3digo
        foreach ($phase['steps'] as &$step) {
            if ($step['id'] == '2.5') {
                foreach ($step['tasks'] as &$task) {
                    if ($task['description'] === 'Criar README.md para cada pacote') {
                        $task['comment'] = 'Incluir instruu00e7u00f5es de instalau00e7u00e3o, configurau00e7u00e3o e uso bu00e1sico';
                    } elseif ($task['description'] === 'Adicionar exemplos de uso para cada pacote') {
                        $task['comment'] = 'Criar exemplos pru00e1ticos de uso para os operadores e QueryBuilder';
                    } elseif ($task['description'] === 'Garantir que todas as classes tenham comentu00e1rios PHPDoc adequados') {
                        $task['description'] = 'Garantir que todas as classes tenham comentu00e1rios PHPDoc adequados';
                    }
                }
            }
        }
    }
}

// Salvar o arquivo JSON atualizado
file_put_contents($roadmapFile, json_encode($roadmap, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "Codificau00e7u00e3o final do roadmap corrigida com sucesso!\n";
