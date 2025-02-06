<?php

namespace App\Service\Stream\Strategy;

use App\Entity\Order;
use App\Service\Stream\DTO\StreamDirective;
use App\Service\Stream\Enum\EnumStreamAction;
use App\Service\Stream\Enum\EnumStreamComponent;

/**
 * @extends AbstractStreamStrategy<Order>
 */
class StreamOrderStrategy extends AbstractStreamStrategy
{
    public function publishCreate(object $order): void
    {
        $topic = "wave_{$order->getWave()->getUlid()}";

        /**
         * @var array<StreamDirective> $directives;
         */
        $directives = [
            [
                "entity" => $order,
                "action" => EnumStreamAction::APPEND,
                "target" => EnumStreamComponent::TABLE_ORDER,
                "component" => EnumStreamComponent::TABLE_ROW_ORDER,
                "context" => ["order" => $order]
            ],
            [
                "entity" => $order,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::TABLE_ROW_WAVE,
                "component" => EnumStreamComponent::TABLE_ROW_WAVE,
                "context" => ["wave" => $order->getWave()]
            ]
        ];

        $payload = $this->streamInterface->renderStreamComponents($topic, $this->buildDirectives($directives));

        $this->publishOnTopic("wave-{$order->getWave()->getUlid()}", $payload);
    }

    public function publishUpdate(object $order): void
    {
    }
}
