<?php

namespace App\Service;

use App\Service\Contract\EmailInterface;
use App\Service\Contract\ValidatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class EmailService implements EmailInterface
{
    public function __construct(
        private MailerInterface $mailerInterface,
        private ValidatorInterface $validatorInterface,
    ) {
    }

    /**
     * Sends templated email.
     *
     * @param array<string, mixed> $context
     */
    public function sendTemplatedMail(string $recipient, string $subject, string $template, array $context = []): void
    {
        $this->validatorInterface->validateEmailRecipient($recipient);
        $this->validatorInterface->validateEmailSubject($subject);
        $this->validatorInterface->validateEmailTemplate($template);

        try {
            $mail = (new TemplatedEmail())
                ->to($recipient)
                ->subject($subject)
                ->htmlTemplate($template)
                ->context($context);

            $this->mailerInterface->send($mail);
        } catch (TransportExceptionInterface $error) {
            throw new \RuntimeException("Email could not be sent.");
        }
    }
}
