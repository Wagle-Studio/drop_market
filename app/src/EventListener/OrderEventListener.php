<?php

namespace App\EventListener;

use App\Entity\Order;
use App\Service\Stream\Contract\StreamStrategyFactoryInterface;
use App\Service\Stream\StreamStrategy;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: "postPersist", entity: Order::class)]
class OrderEventListener
{
    /**
     * @param StreamStrategyFactoryInterface<Order> $streamStrategyFactoryInterface
     */
    public function __construct(private StreamStrategyFactoryInterface $streamStrategyFactoryInterface)
    {
    }

    public function postPersist(Order $order, PostPersistEventArgs $event): void
    {
        $strategy = $this->streamStrategyFactoryInterface->createStrategy($order);
        $streamStrategy = new StreamStrategy($strategy);

        $streamStrategy->publishCreate($order);
    }
}
