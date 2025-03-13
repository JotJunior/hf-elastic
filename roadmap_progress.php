<?php

declare(strict_types=1);

// Carregar o arquivo JSON
$roadmapFile = '/Users/jot/Projects/Aevum/libs/hf-elastic/split_package_roadmap.json';
$roadmap = json_decode(file_get_contents($roadmapFile), true);

// Funu00e7u00e3o para calcular o progresso de uma lista de tarefas
function calculateTasksProgress(array $tasks): array {
    $total = count($tasks);
    $completed = 0;
    $inProgress = 0;
    
    foreach ($tasks as $task) {
        if ($task['status'] === 'completed') {
            $completed++;
        } elseif ($task['status'] === 'in_progress') {
            $inProgress++;
        }
    }
    
    $percentCompleted = ($total > 0) ? round(($completed / $total) * 100, 2) : 0;
    $percentInProgress = ($total > 0) ? round(($inProgress / $total) * 100, 2) : 0;
    $percentPending = 100 - $percentCompleted - $percentInProgress;
    
    return [
        'total' => $total,
        'completed' => $completed,
        'in_progress' => $inProgress,
        'pending' => $total - $completed - $inProgress,
        'percent_completed' => $percentCompleted,
        'percent_in_progress' => $percentInProgress,
        'percent_pending' => $percentPending
    ];
}

// Calcular progresso por fase
$phaseProgress = [];
$overallTasks = [];

foreach ($roadmap['phases'] as $phase) {
    $phaseTasks = [];
    
    foreach ($phase['steps'] as $step) {
        $phaseTasks = array_merge($phaseTasks, $step['tasks']);
        $overallTasks = array_merge($overallTasks, $step['tasks']);
    }
    
    $progress = calculateTasksProgress($phaseTasks);
    $phaseProgress[$phase['id']] = [
        'name' => $phase['name'],
        'progress' => $progress
    ];
}

// Calcular progresso geral
$overallProgress = calculateTasksProgress($overallTasks);

// Exibir resultados
echo "\n=== RESUMO GERAL DO ROADMAP ===\n\n";

echo "Progresso Geral do Projeto:\n";
echo "  Total de tarefas: {$overallProgress['total']}\n";
echo "  Tarefas concluu00eddas: {$overallProgress['completed']} ({$overallProgress['percent_completed']}%)\n";
echo "  Tarefas em andamento: {$overallProgress['in_progress']} ({$overallProgress['percent_in_progress']}%)\n";
echo "  Tarefas pendentes: {$overallProgress['pending']} ({$overallProgress['percent_pending']}%)\n\n";

echo "Progresso por Fase:\n";
foreach ($phaseProgress as $phaseId => $data) {
    echo "  Fase {$phaseId}: {$data['name']}\n";
    echo "    Total de tarefas: {$data['progress']['total']}\n";
    echo "    Concluu00eddas: {$data['progress']['completed']} ({$data['progress']['percent_completed']}%)\n";
    echo "    Em andamento: {$data['progress']['in_progress']} ({$data['progress']['percent_in_progress']}%)\n";
    echo "    Pendentes: {$data['progress']['pending']} ({$data['progress']['percent_pending']}%)\n\n";
}
