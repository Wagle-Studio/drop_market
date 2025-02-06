<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Service\ValidatorService;
use InvalidArgumentException;

class ValidatorServiceTest extends TestCase
{
    private ValidatorService $validator;

    protected function setUp(): void
    {
        $this->validator = new ValidatorService();
    }

    /**
     * @group unit
     */
    public function testValidateEmailForConfirmationSignatureLogsErrorForInvalidEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid email for generate email confirmation signature.");

        $this->validator->validateEmailForConfirmationSignature("invalid-email");
    }

    /**
     * @group unit
     */
    public function testValidateEmailForConfirmationSucceedsForValidEmail(): void
    {
        $this->expectNotToPerformAssertions();
        $this->validator->validateEmailForConfirmationSignature("valid@example.com");
    }

    /**
     * @group unit
     */
    public function testValidateEmailRecipientLogsErrorForInvalidEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid recipient email address provided.");

        $this->validator->validateEmailRecipient("invalid-email");
    }

    /**
     * @group unit
     */
    public function testValidateEmailRecipientSucceedsForValidEmail(): void
    {
        $this->expectNotToPerformAssertions();
        $this->validator->validateEmailRecipient("valid@example.com");
    }

    /**
     * @group unit
     */
    public function testValidateEmailSubjectLogsErrorForForEmptySubject(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Email subject must not be empty.");

        $this->validator->validateEmailSubject("");
    }

    /**
     * @group unit
     */
    public function testValidateEmailSubjectSucceedsForValidSubject(): void
    {
        $this->expectNotToPerformAssertions();
        $this->validator->validateEmailSubject("Valid email subject");
    }

    /**
     * @group unit
     */
    public function testValidateEmailTemplateLogsErrorForEmptyTemplate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Email template must not be empty.");

        $this->validator->validateEmailTemplate("");
    }

    /**
     * @group unit
     */
    public function testValidateEmailTemplateSucceedsForValidTemplate(): void
    {
        $this->expectNotToPerformAssertions();
        $this->validator->validateEmailTemplate("./valid/email/template");
    }
}
