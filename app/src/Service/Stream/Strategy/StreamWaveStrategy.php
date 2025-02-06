<?php

namespace App\Service\Stream\Strategy;

use App\Entity\Wave;
use App\Service\Stream\DTO\StreamDirective;
use App\Service\Stream\Enum\EnumStreamAction;
use App\Service\Stream\Enum\EnumStreamComponent;

/**
 * @extends AbstractStreamStrategy<Wave>
 */
class StreamWaveStrategy extends AbstractStreamStrategy
{
    public function publishCreate(object $wave): void
    {
        $topic = "shop_{$wave->getShop()->getUlid()}";

        /**
         * @var array<StreamDirective> $directives;
         */
        $directives = [
            [
                "entity" => $wave,
                "action" => EnumStreamAction::APPEND,
                "target" => EnumStreamComponent::TABLE_WAVE,
                "component" => EnumStreamComponent::TABLE_ROW_WAVE,
                "context" => ["wave" => $wave]
            ],
            [
                "entity" => $wave,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::FORM_ORDER,
                "component" => EnumStreamComponent::FORM_ORDER,
                "context" => ["shop" => $wave->getShop()]
            ]
        ];

        $payload = $this->streamInterface->renderStreamComponents($topic, $this->buildDirectives($directives));

        $this->publishOnTopic("shop-{$wave->getShop()->getUlid()}", $payload);
    }

    public function publishUpdate(object $wave): void
    {
        $waveTopic = "wave_{$wave->getUlid()}";
        $shopTopic = "shop_{$wave->getShop()->getUlid()}";

        $waveDirectives = [
            [
                "entity" => $wave,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::CARD_ADMIN_SHOP_WAVE_READ,
                "component" => EnumStreamComponent::CARD_ADMIN_SHOP_WAVE_READ,
                "context" => ["wave" => $wave]
            ],
            [
                "entity" => $wave,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::CARD_ADMIN_SHOP_WAVE_EDIT,
                "component" => EnumStreamComponent::CARD_ADMIN_SHOP_WAVE_EDIT,
                "context" => ["wave" => $wave]
            ],
            [
                "entity" => $wave,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::TABLE_ROW_WAVE,
                "component" => EnumStreamComponent::TABLE_ROW_WAVE,
                "context" => ["wave" => $wave]
            ]
        ];

        $shopDirectives = [
            [
                "entity" => $wave,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::FORM_ORDER,
                "component" => EnumStreamComponent::FORM_ORDER,
                "context" => ["shop" => $wave->getShop()]
            ]
        ];

        $wavePayload = $this->streamInterface->renderStreamComponents(
            $waveTopic,
            $this->buildDirectives($waveDirectives)
        );
        $shopPayload = $this->streamInterface->renderStreamComponents(
            $shopTopic,
            $this->buildDirectives($shopDirectives)
        );

        $this->publishOnTopic("wave-{$wave->getUlid()}", $wavePayload);
        $this->publishOnTopic("shop-{$wave->getShop()->getUlid()}", $shopPayload);
    }
}
