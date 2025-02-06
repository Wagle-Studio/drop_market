<?php

namespace App\Service\Stream\Contract;

use App\Entity\Order;
use App\Entity\Shop;
use App\Entity\User;
use App\Entity\Wave;

/**
 * @template TEntity of object
 */
interface StreamStrategyFactoryInterface
{
    /**
     * @return  AbstractStreamStrategyInterface<TEntity>
     */
    public function createStrategy(User|Shop|Wave|Order $entity): AbstractStreamStrategyInterface;
}
