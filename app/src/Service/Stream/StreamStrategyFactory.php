<?php

namespace App\Service\Stream;

use App\Entity\Order;
use App\Entity\Shop;
use App\Entity\User;
use App\Entity\Wave;
use App\Service\Stream\Contract\AbstractStreamStrategyInterface;
use App\Service\Stream\Contract\StreamStrategyFactoryInterface;
use App\Service\Stream\Strategy\StreamOrderStrategy;
use App\Service\Stream\Strategy\StreamShopStrategy;
use App\Service\Stream\Strategy\StreamUserStrategy;
use App\Service\Stream\Strategy\StreamWaveStrategy;
use InvalidArgumentException;

/**
 * @template TEntity of object
 * @implements StreamStrategyFactoryInterface<TEntity>
 */
class StreamStrategyFactory implements StreamStrategyFactoryInterface
{
    /**
     * @var array<class-string<User|Shop|Wave|Order>, AbstractStreamStrategyInterface<TEntity>>
     */
    private array $strategies = [];

    /**
     * @param iterable<StreamUserStrategy|StreamShopStrategy|StreamWaveStrategy|StreamOrderStrategy> $strategies
     */
    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $strategy) {
            $relatedClass = match (get_class($strategy)) {
                StreamUserStrategy::class => User::class,
                StreamShopStrategy::class => Shop::class,
                StreamWaveStrategy::class => Wave::class,
                StreamOrderStrategy::class => Order::class,
                default => throw new InvalidArgumentException("Unknown related class for strategy"),
            };

            $this->strategies[$relatedClass] = $strategy;
        }
    }

    /**
     * @return  AbstractStreamStrategyInterface<TEntity>
     */
    public function createStrategy(User|Shop|Wave|Order $entity): AbstractStreamStrategyInterface
    {
        $class = get_class($entity);

        if (!isset($this->strategies[$class])) {
            throw new InvalidArgumentException("No strategy defined for entity class: $class");
        }

        return $this->strategies[$class];
    }
}
