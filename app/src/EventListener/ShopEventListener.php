<?php

namespace App\EventListener;

use App\Entity\Shop;
use App\Service\Stream\Contract\StreamStrategyFactoryInterface;
use App\Service\Stream\StreamStrategy;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postUpdate, method: "postUpdate", entity: Shop::class)]
class ShopEventListener
{
    /**
     * @param StreamStrategyFactoryInterface<Shop> $streamStrategyFactoryInterface
     */
    public function __construct(private StreamStrategyFactoryInterface $streamStrategyFactoryInterface)
    {
    }

    public function postUpdate(Shop $shop, PostUpdateEventArgs $event): void
    {
        $strategy = $this->streamStrategyFactoryInterface->createStrategy($shop);
        $streamStrategy = new StreamStrategy($strategy);

        $streamStrategy->publishUpdate($shop);
    }
}
