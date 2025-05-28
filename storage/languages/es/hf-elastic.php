<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */
return [
    'error_occurred' => 'Se produjo un error: :message',
    'not_found' => 'Recurso no encontrado',
    'invalid_request' => 'Solicitud inválida',
    'query_error' => 'Error al ejecutar la consulta: :message',
    'invalid_query' => 'Estructura de consulta inválida',
    'empty_result' => 'No se encontraron resultados',
    'document_created' => 'Documento creado con éxito',
    'document_updated' => 'Documento actualizado con éxito',
    'document_deleted' => 'Documento eliminado con éxito',
    'document_not_found' => 'Documento no encontrado',
    'document_already_exists' => 'El documento ya existe',
    'index_created' => 'Índice creado con éxito',
    'index_updated' => 'Índice actualizado con éxito',
    'index_deleted' => 'Índice eliminado con éxito',
    'index_not_found' => 'Índice no encontrado',
    'index_already_exists' => 'El índice <fg=cyan>:index</> ya existe',
    'validation_failed' => 'Validación fallida',
    'required_field' => 'El campo :field es obligatorio',
    'invalid_field' => 'El campo :field es inválido',
    'connection_error' => 'Error de conexión: :message',
    'timeout_error' => 'La solicitud agotó el tiempo de espera',
    'unsupported_operator' => 'Operador no soportado: :operator',
    'invalid_file' => ':file no es un archivo o URL válido.',
    'unreadable_file' => 'No se pudo leer :file.',
    'invalid_template_format' => 'Solo puede usar una de las opciones --json-schema o --json',
    'console_no_migration' => '<fg=yellow>[INFO]</> No se encontraron migraciones para procesar.',
    'console_index_created' => '<fg=green>[OK]</> Índice :index creado.',
    'console_missing_directory' => '<fg=red>[ERROR]</> Directorio de migración faltante :dir',
    'console_migration_directory_failed' => '<fg=red>[ERROR]</> No se pudo crear el directorio de migración',
    'console_invalid_option' => 'Solo puede usar una de las opciones --json-schema o --json',
    'console_migrate_command' => 'Ejecute <fg=yellow>`php bin/hyperf.php elastic:migrate`</> para aplicar la migración.',
    'no_migration' => 'No se encontraron migraciones para procesar.',
];
