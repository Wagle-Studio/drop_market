<?php

namespace App\Service\Stream\DTO;

use App\Entity\Order;
use App\Entity\Shop;
use App\Entity\User;
use App\Entity\Wave;
use App\Service\Stream\Enum\EnumStreamAction;
use App\Service\Stream\Enum\EnumStreamComponent;
use InvalidArgumentException;

class StreamDirective
{
    private readonly User|Shop|Wave|Order $entity;
    private readonly EnumStreamAction $action;
    private readonly EnumStreamComponent $target;
    private readonly EnumStreamComponent $component;
    private readonly array $context;

    /**
     * @param array<string, User|Shop|Wave|Order|EnumStreamAction|EnumStreamComponent|array<string, object>> $directive
     */
    public function __construct(array $directive)
    {
        if (!isset($directive["entity"]) || !$this->isValidEntity($directive["entity"])) {
            throw new InvalidArgumentException("The 'entity' must be an instance of User, Shop, Wave, or Order.");
        }

        if (!isset($directive['action']) || !$directive['action'] instanceof EnumStreamAction) {
            throw new InvalidArgumentException("The 'action' must be an instance of EnumStreamAction.");
        }

        if (!isset($directive['target']) || !$directive['target'] instanceof EnumStreamComponent) {
            throw new InvalidArgumentException("The 'target' must be an instance of EnumStreamComponent.");
        }

        if (!isset($directive['component']) || !$directive['component'] instanceof EnumStreamComponent) {
            throw new InvalidArgumentException("The 'component' must be an instance of EnumStreamComponent.");
        }

        $this->entity = $directive["entity"];
        $this->action = $directive['action'];
        $this->target = $directive['target'];
        $this->component = $directive['component'];
        $this->context = $directive['context'] ?? [];
    }

    public function getEntity(): User|Shop|Wave|Order
    {
        return $this->entity;
    }

    public function getAction(): EnumStreamAction
    {
        return $this->action;
    }

    public function getTarget(): EnumStreamComponent
    {
        return $this->target;
    }

    public function getComponent(): EnumStreamComponent
    {
        return $this->component;
    }

    /**
     * @return array<string, object>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    public function isValidEntity(object $entity): bool
    {
        return $entity instanceof User
            || $entity instanceof Shop
            || $entity instanceof Wave
            || $entity instanceof Order;
    }
}
