<?php

namespace App\Service\Stream\DTO;

use App\Service\Stream\Enum\EnumStreamComponent;

class StreamComponent
{
    private readonly string $name;
    private readonly string $type;
    private readonly bool $useForm;
    private readonly ?string $formType;
    private readonly ?string $formName;

    public function __construct(EnumStreamComponent $component)
    {
        $this->name = $component->value;

        $configuation = $component->getConfiguration();

        $this->type = $configuation["type"];
        $this->useForm = isset($configuation["formType"]);
        $this->formType = $configuation["formType"] ?? null;
        $this->formName = $configuation["formName"] ?? null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function useForm(): bool
    {
        return $this->useForm;
    }

    public function getFormType(): ?string
    {
        return $this->formType;
    }

    public function getFormName(): ?string
    {
        return $this->formName;
    }
}
