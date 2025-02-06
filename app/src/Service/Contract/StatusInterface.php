<?php

namespace App\Service\Contract;

interface StatusInterface
{
    public function setStatus(mixed $entity, string $statusClassName, string $statusConst): mixed;
}
