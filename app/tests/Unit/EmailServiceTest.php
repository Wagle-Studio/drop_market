<?php

namespace App\Tests\Unit;

use App\Service\Contract\ValidatorInterface;
use App\Service\EmailService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;

class EmailServiceTest extends TestCase
{
    private MailerInterface&MockObject $mailerMock;
    private ValidatorInterface&MockObject $validatorMock;
    private EmailService $emailService;

    protected function setUp(): void
    {
        $this->mailerMock = $this->createMock(MailerInterface::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->emailService = new EmailService($this->mailerMock, $this->validatorMock);
    }

    /**
     * @group unit
     */
    public function testSendTemplatedMailValidatesInputs(): void
    {
        $this->validatorMock->expects($this->once())
            ->method("validateEmailRecipient")
            ->with("recipient@example.com");

        $this->validatorMock->expects($this->once())
            ->method("validateEmailSubject")
            ->with("Test Subject");

        $this->validatorMock->expects($this->once())
            ->method("validateEmailTemplate")
            ->with("emails/template.html.twig");

        $this->mailerMock->expects($this->once())
            ->method("send")
            ->with($this->callback(function (TemplatedEmail $email) {
                return $email->getTo()[0]->getAddress() === "recipient@example.com" &&
                    $email->getSubject() === "Test Subject" &&
                    $email->getHtmlTemplate() === "emails/template.html.twig" &&
                    $email->getContext() === ["key" => "value"];
            }));

        $this->emailService->sendTemplatedMail(
            "recipient@example.com",
            "Test Subject",
            "emails/template.html.twig",
            ["key" => "value"]
        );
    }

    /**
     * @group unit
     */
    public function testSendTemplatedMailThrowsExceptionForInvalidEmail(): void
    {
        $this->validatorMock->expects($this->once())
            ->method("validateEmailRecipient")
            ->with("invalid-email")
            ->willThrowException(new \InvalidArgumentException("Invalid email address."));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid email address.");

        $this->emailService->sendTemplatedMail(
            "invalid-email",
            "Test Subject",
            "emails/template.html.twig",
            []
        );
    }


    /**
     * @group unit
     */
    public function testSendTemplatedMailThrowsExceptionForInvalidSubject(): void
    {
        $this->validatorMock->expects($this->once())
            ->method("validateEmailSubject")
            ->with("")
            ->willThrowException(new \InvalidArgumentException("Email subject must not be empty."));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Email subject must not be empty.");

        $this->emailService->sendTemplatedMail(
            "invalid-email",
            "",
            "emails/template.html.twig",
            []
        );
    }


    /**
     * @group unit
     */
    public function testSendTemplatedMailThrowsExceptionForInvalidTemplate(): void
    {
        $this->validatorMock->expects($this->once())
            ->method("validateEmailTemplate")
            ->with("")
            ->willThrowException(new \InvalidArgumentException("Email template must not be empty."));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Email template must not be empty.");

        $this->emailService->sendTemplatedMail(
            "invalid-email",
            "Test Subject",
            "",
            []
        );
    }


    /**
     * @group unit
     */
    public function testSendTemplatedMailLogsErrorAndThrowsRuntimeExceptionOnTransportFailure(): void
    {
        $this->validatorMock->expects($this->once())
            ->method("validateEmailRecipient")
            ->with("recipient@example.com");

        $this->validatorMock->expects($this->once())
            ->method("validateEmailSubject")
            ->with("Test Subject");

        $this->validatorMock->expects($this->once())
            ->method("validateEmailTemplate")
            ->with("emails/template.html.twig");

        $this->mailerMock->expects($this->once())
            ->method("send")
            ->willThrowException(new TransportException("Email could not be sent."));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Email could not be sent.");

        $this->emailService->sendTemplatedMail(
            "recipient@example.com",
            "Test Subject",
            "emails/template.html.twig",
            []
        );
    }


    /**
     * @group unit
     */
    public function testSendTemplatedMailBuildsCorrectEmail(): void
    {
        $this->mailerMock->expects($this->once())
            ->method("send")
            ->with($this->callback(function (TemplatedEmail $email) {
                return $email->getTo()[0]->getAddress() === "recipient@example.com" &&
                    $email->getSubject() === "Test Subject" &&
                    $email->getHtmlTemplate() === "emails/template.html.twig";
            }));

        $this->emailService->sendTemplatedMail(
            "recipient@example.com",
            "Test Subject",
            "emails/template.html.twig",
            ["key" => "value"]
        );
    }
}
