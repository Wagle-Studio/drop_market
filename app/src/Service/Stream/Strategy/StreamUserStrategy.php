<?php

namespace App\Service\Stream\Strategy;

use App\Entity\User;
use App\Service\Stream\DTO\StreamDirective;
use App\Service\Stream\Enum\EnumStreamAction;
use App\Service\Stream\Enum\EnumStreamComponent;

/**
 * @extends AbstractStreamStrategy<User>
 */
class StreamUserStrategy extends AbstractStreamStrategy
{
    public function publishCreate(object $user): void
    {
    }

    public function publishUpdate(object $user): void
    {
        $topic = "user_{$user->getUlid()}";

        /**
         * @var array<StreamDirective> $directives;
         */
        $directives = [
            [
                "entity" => $user,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::HEADER_PROFILE,
                "component" => EnumStreamComponent::HEADER_PROFILE,
            ],
            [
                "entity" => $user,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::CARD_PROFILE_READ,
                "component" => EnumStreamComponent::CARD_PROFILE_READ,
            ],
            [
                "entity" => $user,
                "action" => EnumStreamAction::REPLACE,
                "target" => EnumStreamComponent::CARD_PROFILE_EDIT,
                "component" => EnumStreamComponent::CARD_PROFILE_EDIT,
            ]
        ];

        $payload = $this->streamInterface->renderStreamComponents($topic, $this->buildDirectives($directives));

        $this->publishOnTopic("user-{$user->getUlid()}", $payload);
    }
}
