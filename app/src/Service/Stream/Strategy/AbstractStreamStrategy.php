<?php

namespace App\Service\Stream\Strategy;

use App\Entity\Order;
use App\Entity\Shop;
use App\Entity\User;
use App\Entity\Wave;
use App\Service\Stream\Contract\AbstractStreamStrategyInterface;
use App\Service\Stream\Contract\StreamInterface;
use App\Service\Stream\DTO\StreamDirective;
use App\Service\Stream\Enum\EnumStreamAction;
use App\Service\Stream\Enum\EnumStreamComponent;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

/**
 * @template TEntity of object
 * @implements AbstractStreamStrategyInterface<TEntity>
 */
abstract class AbstractStreamStrategy implements AbstractStreamStrategyInterface
{
    public function __construct(private HubInterface $hubInterface, protected StreamInterface $streamInterface)
    {
    }

    /**
     * @param array<string, User|Shop|Wave|Order|EnumStreamAction|EnumStreamComponent|array<string, object>> $directives
     * @return array<StreamDirective>
     */
    public function buildDirectives(mixed $directives): array
    {
        return array_map(
            fn($directive) => new StreamDirective($directive),
            $directives
        );
    }

    /**
     * @param array<int, string> $payload
     */
    public function publishOnTopic(string $topic, array $payload): void
    {
        $update = new Update($topic, json_encode($payload));

        $this->hubInterface->publish($update);
    }
}
