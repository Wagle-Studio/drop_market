<?php

namespace App\EventListener;

use App\Entity\Wave;
use App\Service\Stream\Contract\StreamStrategyFactoryInterface;
use App\Service\Stream\StreamStrategy;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: "postPersist", entity: Wave::class)]
#[AsEntityListener(event: Events::postUpdate, method: "postUpdate", entity: Wave::class)]
class WaveEventListener
{
    /**
     * @param StreamStrategyFactoryInterface<Wave> $streamStrategyFactoryInterface
     */
    public function __construct(private StreamStrategyFactoryInterface $streamStrategyFactoryInterface)
    {
    }

    public function postPersist(Wave $wave, PostPersistEventArgs $event): void
    {
        $strategy = $this->streamStrategyFactoryInterface->createStrategy($wave);
        $streamStrategy = new StreamStrategy($strategy);

        $streamStrategy->publishCreate($wave);
    }

    public function postUpdate(Wave $wave, PostUpdateEventArgs $event): void
    {
        $strategy = $this->streamStrategyFactoryInterface->createStrategy($wave);
        $streamStrategy = new StreamStrategy($strategy);

        $streamStrategy->publishUpdate($wave);
    }
}
