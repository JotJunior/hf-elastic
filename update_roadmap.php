<?php

// Carregar o arquivo JSON
$roadmapFile = '/Users/jot/Projects/Aevum/libs/hf-elastic/split_package_roadmap.json';
$roadmap = json_decode(file_get_contents($roadmapFile), true);

// Atualizar o status das tarefas da fase 4.1 (Extração do Código do QueryBuilder)
foreach ($roadmap['phases'] as &$phase) {
    if ($phase['id'] == 4) { // Fase 4: Implementação do Pacote QueryBuilder
        foreach ($phase['steps'] as &$step) {
            if ($step['id'] == '4.1') { // Passo 4.1: Extração do Código do QueryBuilder
                foreach ($step['tasks'] as &$task) {
                    if ($task['description'] === 'Ajustar namespaces e imports') {
                        $task['status'] = 'completed';
                        $task['comment'] = 'Ajustados namespaces para Jot\\HfElasticQuery e imports para usar o pacote core';
                    } elseif ($task['description'] === 'Atualizar dependências para usar o pacote core') {
                        $task['status'] = 'completed';
                        $task['comment'] = 'QueryBuilder agora depende explicitamente do pacote core para interfaces e serviços compartilhados';
                    } elseif ($task['description'] === 'Implementar interfaces definidas no core') {
                        $task['status'] = 'completed';
                        $task['comment'] = 'Implementadas interfaces do core no pacote query, incluindo ElasticClient';
                    } elseif ($task['description'] === 'Adaptar código para funcionar com a nova estrutura') {
                        $task['status'] = 'completed';
                        $task['comment'] = 'Implementados QueryContext, OperatorRegistry e diversos operadores para construção de consultas';
                    }
                }
            }
        }
    }
}

// Salvar o arquivo JSON atualizado
file_put_contents($roadmapFile, json_encode($roadmap, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "Roadmap atualizado com sucesso!\n";
