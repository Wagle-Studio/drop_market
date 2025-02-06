<?php

namespace App\Service\Contract;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;

interface SecurityInterface
{
    public function generateEmailConfirmationSignature(User $user): VerifyEmailSignatureComponents;

    /**
     * @return array<string, mixed>
     */
    public function buildEmailConfirmationTemplateContext(User $user): array;
    public function verifyEmailRequestSignature(Request $request, User $user): void;
}
