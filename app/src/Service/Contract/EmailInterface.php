<?php

namespace App\Service\Contract;

interface EmailInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function sendTemplatedMail(string $recipient, string $subject, string $template, array $context = []): void;
}
