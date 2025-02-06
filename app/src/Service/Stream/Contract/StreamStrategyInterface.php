<?php

namespace App\Service\Stream\Contract;

/**
 * @template TEntity of object
 */
interface StreamStrategyInterface
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
