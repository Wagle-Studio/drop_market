<?php

namespace App\Service\Stream\Strategy;

use App\Entity\Shop;
use App\Service\Stream\DTO\StreamDirective;
use App\Service\Stream\Enum\EnumStreamAction;
use App\Service\Stream\Enum\EnumStreamComponent;

/**
 * @extends AbstractStreamStrategy<Shop>
 */
class StreamShopStrategy extends AbstractStreamStrategy
{
    public function publishCreate(object $shop): void
    {
    }

    public function publishUpdate(object $shop): void
    {
        $topic = "shop_{$shop->getUlid()}";

        /**
         * @var array<StreamDirective> $directives;
         */
        $directives = [
            [
                "entity" => $shop,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::WALLET_HEADER_ADMIN,
                "component" => EnumStreamComponent::WALLET_HEADER_ADMIN,
                "context" => ["shop" => $shop]
            ],
            [
                "entity" => $shop,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::TABLE_ROW_SHOP,
                "component" => EnumStreamComponent::TABLE_ROW_SHOP,
                "context" => ["shop" => $shop]
            ],
            [
                "entity" => $shop,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::CARD_ADMIN_SHOP_EDIT,
                "component" => EnumStreamComponent::CARD_ADMIN_SHOP_EDIT,
                "context" => ["shop" => $shop],
            ]
        ];

        $payload = $this->streamInterface->renderStreamComponents($topic, $this->buildDirectives($directives));

        $this->publishOnTopic("shop-{$shop->getUlid()}", $payload);
    }
}
