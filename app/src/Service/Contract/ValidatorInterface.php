<?php

namespace App\Service\Contract;

interface ValidatorInterface
{
    public function validateEntityInstance(mixed $entity, string $classname): void;
    public function validateEmailForConfirmationSignature(string $email): void;
    public function validateEmailRecipient(string $email): void;
    public function validateEmailSubject(string $subject): void;
    public function validateEmailTemplate(string $template): void;
}
