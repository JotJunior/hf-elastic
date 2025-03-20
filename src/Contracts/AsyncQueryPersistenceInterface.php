<?php

declare(strict_types=1);

namespace Jot\HfElastic\Contracts;

/**
 * Interface for building asynchronous Elasticsearch queries with coroutine support.
 */
interface AsyncQueryPersistenceInterface
{
    /**
     * Asynchronous version of count method for use with coroutines in Hyperf 3.1
     * @return \Hyperf\Coroutine\Coroutine\Locker
     */
    public function countAsync();

    /**
     * Asynchronous version of execute method for use with coroutines in Hyperf 3.1
     * @return \Hyperf\Coroutine\Coroutine\Locker
     */
    public function executeAsync();

    /**
     * Asynchronous version of delete method for use with coroutines in Hyperf 3.1
     * @param string $id The unique identifier of the resource to be deleted.
     * @param bool $logicalDeletion Determines if the deletion should be logical (true) or physical (false). Default is true.
     * @return \Hyperf\Coroutine\Coroutine\Locker
     */
    public function deleteAsync(string $id, bool $logicalDeletion = true);

    /**
     * Asynchronous version of update method for use with coroutines in Hyperf 3.1
     * @param string $id The unique identifier of the record to be updated.
     * @param array $data The data to be updated for the specified record.
     * @return \Hyperf\Coroutine\Coroutine\Locker
     */
    public function updateAsync(string $id, array $data);

    /**
     * Asynchronous version of insert method for use with coroutines in Hyperf 3.1
     * @param array $data The data to be inserted.
     * @return \Hyperf\Coroutine\Coroutine\Locker
     */
    public function insertAsync(array $data);

    /**
     * Asynchronous version of getDocumentVersion method for use with coroutines in Hyperf 3.1
     * @param string $id The unique identifier of the document.
     * @return \Hyperf\Coroutine\Coroutine\Locker
     */
    public function getDocumentVersionAsync(string $id);
}
