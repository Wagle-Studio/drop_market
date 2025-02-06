<?php

namespace App\Service\Stream;

use App\Service\Stream\Contract\AbstractStreamStrategyInterface;
use App\Service\Stream\Contract\StreamStrategyInterface;

/**
 * @template TEntity of object
 * @implements StreamStrategyInterface<TEntity>
 */
class StreamStrategy implements StreamStrategyInterface
{
    /** @var AbstractStreamStrategyInterface<TEntity> */
    private AbstractStreamStrategyInterface $strategy;

    /**
     * @param AbstractStreamStrategyInterface<TEntity> $strategy
     */
    public function __construct(AbstractStreamStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @param AbstractStreamStrategyInterface<TEntity> $strategy
     */
    public function setStrategy(AbstractStreamStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * @param TEntity $entity
     */
    public function publishCreate(object $entity): void
    {
        $this->strategy->publishCreate($entity);
    }

    /**
     * @param TEntity $entity
     */
    public function publishUpdate(object $entity): void
    {
        $this->strategy->publishUpdate($entity);
    }
}
