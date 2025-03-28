<?php

declare(strict_types=1);

return [
    'hf_elastic' => [
        // General messages
        'error_occurred' => 'An error occurred: :message',
        'not_found' => 'Resource not found',
        'invalid_request' => 'Invalid request',
        
        // Query related messages
        'query_error' => 'Error executing query: :message',
        'invalid_query' => 'Invalid query structure',
        'empty_result' => 'No results found',
        
        // Document related messages
        'document_created' => 'Document created successfully',
        'document_updated' => 'Document updated successfully',
        'document_deleted' => 'Document deleted successfully',
        'document_not_found' => 'Document not found',
        'document_already_exists' => 'Document already exists',
        
        // Index related messages
        'index_created' => 'Index created successfully',
        'index_updated' => 'Index updated successfully',
        'index_deleted' => 'Index deleted successfully',
        'index_not_found' => 'Index not found',
        'index_already_exists' => 'Index already exists',
        
        // Validation messages
        'validation_failed' => 'Validation failed',
        'required_field' => 'The :field field is required',
        'invalid_field' => 'The :field field is invalid',
        
        // Connection related messages
        'connection_error' => 'Connection error: :message',
        'timeout_error' => 'Request timed out',
        
        // Operator related messages
        'unsupported_operator' => 'Unsupported operator: :operator',
        
        // File related messages
        'invalid_file' => ':file is not a valid file or url.',
        'unreadable_file' => ':file could not be read.',
        'invalid_template_format' => 'You can only use one of the options --json-schema or --json'
    ]
];
