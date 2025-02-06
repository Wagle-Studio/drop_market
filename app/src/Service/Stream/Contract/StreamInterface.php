<?php

namespace App\Service\Stream\Contract;

use App\Service\Stream\DTO\StreamDirective;

interface StreamInterface
{
    /**
     * @param array<StreamDirective> $directives
     * @return array<string>
     */
    public function renderStreamComponents(string $topic, array $directives): array;
}
