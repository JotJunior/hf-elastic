<?php

declare(strict_types=1);
/**
 * This file is part of the hf_shield module, a package build for Hyperf framework that is responsible for OAuth2 authentication and access control.
 *
 * @author   Joao Zanon <jot@jot.com.br>
 * @link     https://github.com/JotJunior/hf-shield
 * @license  MIT
 */
return [
    'error_occurred' => 'An error occurred: :message',
    'not_found' => 'Resource not found',
    'invalid_request' => 'Invalid request',
    'query_error' => 'Error executing query: :message',
    'invalid_query' => 'Invalid query structure',
    'empty_result' => 'No results found',
    'document_created' => 'Document created successfully',
    'document_updated' => 'Document updated successfully',
    'document_deleted' => 'Document deleted successfully',
    'document_not_found' => 'Document not found',
    'document_already_exists' => 'Document already exists',
    'index_created' => 'Índice <fg=cyan>:index</> created',
    'index_updated' => 'Índice <fg=cyan>:index</> updated',
    'index_deleted' => 'Índice <fg=cyan>:index</> removed',
    'index_not_found' => 'Index not found',
    'index_already_exists' => 'Index <fg=cyan>:index</> already exists',
    'validation_failed' => 'Validation failed',
    'required_field' => 'The :field field is required',
    'invalid_field' => 'The :field field is invalid',
    'connection_error' => 'Connection error: :message',
    'timeout_error' => 'Request timed out',
    'unsupported_operator' => 'Unsupported operator: :operator',
    'invalid_file' => ':file is not a valid file or url.',
    'unreadable_file' => ':file could not be read.',
    'invalid_template_format' => 'You can only use one of the options --json-schema or --json',
    'console_no_migration' => '<fg=yellow>[INFO]</> No migrations found to process.',
    'console_index_created' => '<fg=green>[OK]</> Index :index created.',
    'console_missing_directory' => '<fg=red>[ERROR]</> Missing migration directory :dir',
    'console_migration_directory_failed' => '<fg=red>[ERROR]</> Failed to create migration directory',
    'console_invalid_option' => 'You can only use one of the options --json-schema or --json',
    'console_migrate_command' => 'Run <fg=yellow>`php bin/hyperf.php elastic:migrate`</> to apply the migration.',
    'no_migration' => 'No migration found',
    'cannot_delete_referenced_document' => 'Cannot delete document due to references in other records.',
];
