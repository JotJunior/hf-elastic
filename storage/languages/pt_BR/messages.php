<?php

declare(strict_types=1);

return [
    'hf_elastic' => [
        // General messages
        'error_occurred' => 'Ocorreu um erro: :message',
        'not_found' => 'Recurso não encontrado',
        'invalid_request' => 'Requisição inválida',
        
        // Query related messages
        'query_error' => 'Erro ao executar consulta: :message',
        'invalid_query' => 'Estrutura de consulta inválida',
        'empty_result' => 'Nenhum resultado encontrado',
        
        // Document related messages
        'document_created' => 'Documento criado com sucesso',
        'document_updated' => 'Documento atualizado com sucesso',
        'document_deleted' => 'Documento excluído com sucesso',
        'document_not_found' => 'Documento não encontrado',
        'document_already_exists' => 'Documento já existe',
        
        // Index related messages
        'index_created' => 'Índice criado com sucesso',
        'index_updated' => 'Índice atualizado com sucesso',
        'index_deleted' => 'Índice excluído com sucesso',
        'index_not_found' => 'Índice não encontrado',
        'index_already_exists' => 'Índice já existe',
        
        // Validation messages
        'validation_failed' => 'Falha na validação',
        'required_field' => 'O campo :field é obrigatório',
        'invalid_field' => 'O campo :field é inválido',
        
        // Connection related messages
        'connection_error' => 'Erro de conexão: :message',
        'timeout_error' => 'Tempo limite da requisição excedido',
        
        // Operator related messages
        'unsupported_operator' => 'Operador não suportado: :operator',
        
        // File related messages
        'invalid_file' => ':file não é um arquivo ou URL válido.',
        'unreadable_file' => ':file não pôde ser lido.',
        'invalid_template_format' => 'Você só pode usar uma das opções --json-schema ou --json'
    ]
];
