<?php

declare(strict_types=1);

// Carregar o arquivo JSON
$roadmapFile = '/Users/jot/Projects/Aevum/libs/hf-elastic/split_package_roadmap.json';
$roadmap = json_decode(file_get_contents($roadmapFile), true);

// Adicionar nova etapa 2.4 para testes especu00edficos dos operadores
$newStep24 = [
    'id' => '2.4',
    'name' => 'Implementau00e7u00e3o de Testes para Operadores',
    'tasks' => [
        [
            'description' => 'Criar testes unitu00e1rios para os novos operadores',
            'status' => 'pending',
            'comment' => 'Implementar testes para NotLikeOperator, BetweenOperator, NotBetweenOperator, ExistsOperator e NotExistsOperator'
        ],
        [
            'description' => 'Implementar testes para a classe QueryBuilder',
            'status' => 'pending',
            'comment' => 'Garantir que a classe QueryBuilder funcione corretamente com todos os operadores'
        ],
        [
            'description' => 'Melhorar cobertura de testes para classes com baixa cobertura',
            'status' => 'pending',
            'comment' => 'Focar nas classes BooleanType (40.00%), DateType (30.00%), HistogramType (33.33%), IpType (37.50%) e SearchAsYouType (20.00%)'
        ]
    ],
    'deliverables' => [
        'Testes unitu00e1rios para todos os operadores',
        'Relatu00f3rio de cobertura de testes melhorado'
    ]
];

// Adicionar nova etapa 2.5 para documentau00e7u00e3o dos pacotes
$newStep25 = [
    'id' => '2.5',
    'name' => 'Documentau00e7u00e3o dos Pacotes',
    'tasks' => [
        [
            'description' => 'Criar README.md para cada pacote',
            'status' => 'pending',
            'comment' => 'Incluir instruu00e7u00f5es de instalau00e7u00e3o, configurau00e7u00e3o e uso bu00e1sico'
        ],
        [
            'description' => 'Adicionar exemplos de uso para cada pacote',
            'status' => 'pending',
            'comment' => 'Criar exemplos pru00e1ticos de uso para os operadores e QueryBuilder'
        ],
        [
            'description' => 'Garantir que todas as classes tenham comentu00e1rios PHPDoc adequados',
            'status' => 'pending',
            'comment' => 'Revisar e completar a documentau00e7u00e3o PHPDoc em todas as classes implementadas'
        ]
    ],
    'deliverables' => [
        'Documentau00e7u00e3o completa para todos os pacotes',
        'Exemplos de uso para os principais componentes'
    ]
];

// Adicionar as novas etapas u00e0 fase 2
foreach ($roadmap['phases'] as &$phase) {
    if ($phase['id'] == 2) { // Fase 2: Preparau00e7u00e3o da Base de Cu00f3digo
        $phase['steps'][] = $newStep24;
        $phase['steps'][] = $newStep25;
        break;
    }
}

// Salvar o arquivo JSON atualizado
file_put_contents($roadmapFile, json_encode($roadmap, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "Roadmap atualizado com sucesso! Adicionadas etapas 2.4 e 2.5 para melhor controle do progresso.\n";
