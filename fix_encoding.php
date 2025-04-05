<?php

declare(strict_types=1);

// Carregar o arquivo JSON
$roadmapFile = '/Users/jot/Projects/Aevum/libs/hf-elastic/split_package_roadmap.json';
$roadmap = json_decode(file_get_contents($roadmapFile), true);

// Corrigir a codificação das novas etapas
foreach ($roadmap['phases'] as &$phase) {
    if ($phase['id'] == 2) { // Fase 2: Preparação da Base de Código
        foreach ($phase['steps'] as &$step) {
            if ($step['id'] == '2.4') {
                $step['name'] = 'Implementação de Testes para Operadores';
                foreach ($step['tasks'] as &$task) {
                    if (strpos($task['description'], 'unitu00e1rios') !== false) {
                        $task['description'] = 'Criar testes unitários para os novos operadores';
                    }
                }
                foreach ($step['deliverables'] as &$deliverable) {
                    if (strpos($deliverable, 'unitu00e1rios') !== false) {
                        $deliverable = 'Testes unitários para todos os operadores';
                    }
                    if (strpos($deliverable, 'Relatu00f3rio') !== false) {
                        $deliverable = 'Relatório de cobertura de testes melhorado';
                    }
                }
            }
            if ($step['id'] == '2.5') {
                $step['name'] = 'Documentação dos Pacotes';
                foreach ($step['tasks'] as &$task) {
                    if (strpos($task['description'], 'instruu00e7u00f5es') !== false) {
                        $task['comment'] = 'Incluir instruções de instalação, configuração e uso básico';
                    }
                    if (strpos($task['description'], 'pru00e1ticos') !== false) {
                        $task['comment'] = 'Criar exemplos práticos de uso para os operadores e QueryBuilder';
                    }
                    if (strpos($task['description'], 'comentu00e1rios') !== false) {
                        $task['comment'] = 'Revisar e completar a documentação PHPDoc em todas as classes implementadas';
                    }
                }
                foreach ($step['deliverables'] as &$deliverable) {
                    if (strpos($deliverable, 'Documentau00e7u00e3o') !== false) {
                        $deliverable = 'Documentação completa para todos os pacotes';
                    }
                }
            }
        }
    }
}

// Salvar o arquivo JSON atualizado
file_put_contents($roadmapFile, json_encode($roadmap, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "Codificação do roadmap corrigida com sucesso!\n";
