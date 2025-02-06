<?php

namespace App\Service\Stream\Contract;

/**
 * @template TEntity of object
 */
interface AbstractStreamStrategyInterface
{
    /**
     * @param TEntity $entity
     */
    public function publishCreate(object $entity): void;

    /**
     * @param TEntity $entity
     */
    public function publishUpdate(object $entity): void;
}
