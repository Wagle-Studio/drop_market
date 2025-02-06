<?php

namespace App\Service;

use App\Entity\User;
use App\Service\Contract\SecurityInterface;
use App\Service\Contract\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SecurityService implements SecurityInterface
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelperInterface,
        private ValidatorInterface $validatorInterface,
    ) {
    }

    /**
     * Generates email signature.
     */
    public function generateEmailConfirmationSignature(User $user): VerifyEmailSignatureComponents
    {
        $this->validatorInterface->validateEmailForConfirmationSignature($user->getEmail());

        return $this->verifyEmailHelperInterface->generateSignature(
            "auth_email_verify",
            (string) $user->getId(),
            (string) $user->getEmail()
        );
    }

    /**
     * Generates email confirmation context for the email template.
     *
     * @return array<string, mixed>
     */
    public function buildEmailConfirmationTemplateContext(User $user): array
    {
        $signature = $this->generateEmailConfirmationSignature($user);

        return [
            "signedUrl" => $signature->getSignedUrl(),
            "expiresAtMessageKey" => $signature->getExpirationMessageKey(),
            "expiresAtMessageData" => $signature->getExpirationMessageData(),
        ];
    }

    /**
     * Verifies email confirmation signature request.
     */
    public function verifyEmailRequestSignature(Request $request, User $user): void
    {
        try {
            $modifiedRequest = clone $request;

            // Used for testing.
            if (strpos($modifiedRequest->getUri(), ":9080") !== false) {
                $modifiedUri = str_replace(":9080", "", $modifiedRequest->getUri());
                $modifiedRequest->server->set("REQUEST_URI", parse_url($modifiedUri, PHP_URL_PATH));
                $modifiedRequest->server->set("QUERY_STRING", parse_url($modifiedUri, PHP_URL_QUERY));
                $modifiedRequest->server->set("HTTP_HOST", parse_url($modifiedUri, PHP_URL_HOST));
                $modifiedRequest->headers->set("Host", parse_url($modifiedUri, PHP_URL_HOST));
            }

            $this->verifyEmailHelperInterface->validateEmailConfirmationFromRequest(
                $modifiedRequest,
                strval($user->getId()),
                $user->getEmail()
            );
        } catch (VerifyEmailExceptionInterface $exception) {
            throw $exception;
        }
    }
}
