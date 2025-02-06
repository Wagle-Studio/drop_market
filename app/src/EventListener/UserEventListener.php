<?php

namespace App\EventListener;

use App\Entity\User;
use App\Service\Stream\Contract\StreamStrategyFactoryInterface;
use App\Service\Stream\StreamStrategy;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postUpdate, method: "postUpdate", entity: User::class)]
class UserEventListener
{
    /**
     * @param StreamStrategyFactoryInterface<User> $streamStrategyFactoryInterface
     */
    public function __construct(private StreamStrategyFactoryInterface $streamStrategyFactoryInterface)
    {
    }

    public function postUpdate(User $user, PostUpdateEventArgs $event): void
    {
        $strategy = $this->streamStrategyFactoryInterface->createStrategy($user);
        $streamStrategy = new StreamStrategy($strategy);

        $streamStrategy->publishUpdate($user);
    }
}
