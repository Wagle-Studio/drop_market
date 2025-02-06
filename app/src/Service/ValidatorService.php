<?php

namespace App\Service;

use App\Service\Contract\ValidatorInterface;
use InvalidArgumentException;

class ValidatorService implements ValidatorInterface
{
    /**
     * Validates entity instance.
     */
    public function validateEntityInstance(mixed $entity, string $classname): void
    {
        if (!$entity instanceof $classname) {
            throw new InvalidArgumentException("The entity doesn't match the expected class.");
        }
    }

    /**
     * Validates email for confirmation signature generation.
     */
    public function validateEmailForConfirmationSignature(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email for generate email confirmation signature.");
        }
    }

    /**
     * Validates email recipient value.
     */
    public function validateEmailRecipient(string $recipient): void
    {
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid recipient email address provided.");
        }
    }

    /**
     * Validates email subject value.
     */
    public function validateEmailSubject(string $subject): void
    {
        if (empty($subject)) {
            throw new InvalidArgumentException("Email subject must not be empty.");
        }
    }

    /**
     * Validates email template value.
     */
    public function validateEmailTemplate(string $template): void
    {
        if (empty($template)) {
            throw new InvalidArgumentException("Email template must not be empty.");
        }
    }
}
