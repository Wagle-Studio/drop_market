<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Service\Contract\ValidatorInterface;
use App\Service\SecurityService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class SecurityServiceTest extends TestCase
{
    private VerifyEmailHelperInterface&MockObject $verifyEmailHelperMock;
    private ValidatorInterface&MockObject $validatorMock;
    private SecurityService $securityService;

    protected function setUp(): void
    {
        $this->verifyEmailHelperMock = $this->createMock(VerifyEmailHelperInterface::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->securityService = new SecurityService(
            $this->verifyEmailHelperMock,
            $this->validatorMock
        );
    }

    /**
     * @group unit
     */
    public function testGenerateEmailConfirmationSignatureCallsValidatorAndHelper(): void
    {
        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);
        $user->method("getEmail")->willReturn("valid@example.com");
        $user->method("getId")->willReturn(1);

        $this->validatorMock->expects($this->once())
            ->method("validateEmailForConfirmationSignature")
            ->with("valid@example.com");

        $expiresAt = new \DateTimeImmutable("+1 hour");
        $signedUrl = "http://example.com/signed-url";
        $generatedAt = time();

        $signatureComponents = new VerifyEmailSignatureComponents(
            $expiresAt,
            $signedUrl,
            $generatedAt
        );

        $this->verifyEmailHelperMock->expects($this->once())
            ->method("generateSignature")
            ->with("auth_email_verify", "1", "valid@example.com")
            ->willReturn($signatureComponents);

        $result = $this->securityService->generateEmailConfirmationSignature($user);

        $this->assertSame($signedUrl, $result->getSignedUrl());
        $this->assertSame($expiresAt, $result->getExpiresAt());
    }

    /**
     * @group unit
     */
    public function testBuildEmailConfirmationTemplateContext(): void
    {
        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);
        $user->method("getEmail")->willReturn("valid@example.com");
        $user->method("getId")->willReturn(1);

        $expiresAt = new \DateTimeImmutable("+1 hour");
        $signedUrl = "http://example.com/signed-url";
        $generatedAt = time();

        $signatureComponents = new VerifyEmailSignatureComponents(
            $expiresAt,
            $signedUrl,
            $generatedAt
        );

        $this->validatorMock->expects($this->once())
            ->method("validateEmailForConfirmationSignature")
            ->with("valid@example.com");

        $this->verifyEmailHelperMock->expects($this->once())
            ->method("generateSignature")
            ->with("auth_email_verify", "1", "valid@example.com")
            ->willReturn($signatureComponents);

        $context = $this->securityService->buildEmailConfirmationTemplateContext($user);

        $this->assertSame($signedUrl, $context["signedUrl"]);
        $this->assertSame($signatureComponents->getExpirationMessageKey(), $context["expiresAtMessageKey"]);
        $this->assertSame($signatureComponents->getExpirationMessageData(), $context["expiresAtMessageData"]);
    }

    /**
     * @group unit
     */
    public function testVerifyEmailRequestSignatureCallsHelperWithCorrectArguments(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);

        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);
        $user->method("getId")->willReturn(1);
        $user->method("getEmail")->willReturn("valid@example.com");

        $expiresAt = new \DateTimeImmutable("+1 hour");
        $signedUrl = "http://example.com/signed-url";
        $generatedAt = time();

        $signatureComponents = new VerifyEmailSignatureComponents(
            $expiresAt,
            $signedUrl,
            $generatedAt
        );

        $this->verifyEmailHelperMock = $this->getMockBuilder(VerifyEmailHelperInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(["validateEmailConfirmationFromRequest"])
            ->onlyMethods(["generateSignature", "validateEmailConfirmation"])
            ->getMock();

        $this->verifyEmailHelperMock->method("generateSignature")
            ->willReturn($signatureComponents);

        $this->verifyEmailHelperMock->expects($this->once())
            ->method("validateEmailConfirmationFromRequest")
            ->with($request, "1", "valid@example.com");

        $this->securityService = new SecurityService($this->verifyEmailHelperMock, $this->validatorMock);

        $this->securityService->verifyEmailRequestSignature($request, $user);
    }

    /**
     * @group unit
     */
    public function testVerifyEmailRequestSignatureThrowsExceptionOnValidationFailure(): void
    {
        /** @var Request&MockObject $request */
        $request = $this->createMock(Request::class);

        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);
        $user->method("getId")->willReturn(1);
        $user->method("getEmail")->willReturn("valid@example.com");

        $this->verifyEmailHelperMock = $this->getMockBuilder(VerifyEmailHelperInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(["validateEmailConfirmationFromRequest"])
            ->onlyMethods(["generateSignature", "validateEmailConfirmation"])
            ->getMock();

        $this->verifyEmailHelperMock->expects($this->once())
            ->method("validateEmailConfirmationFromRequest")
            ->with($request, "1", "valid@example.com")
            ->willThrowException(new ExpiredSignatureException());

        $this->securityService = new SecurityService($this->verifyEmailHelperMock, $this->validatorMock);

        $this->expectException(VerifyEmailExceptionInterface::class);

        $this->securityService->verifyEmailRequestSignature($request, $user);
    }

    /**
     * @group unit
     */
    public function testVerifyEmailRequestSignatureHandlesPort(): void
    {
        $request = Request::create(
            "http://127.0.0.1:9080/auth/email/verification?token=abc123&signature=xyz789",
            "GET"
        );

        /** @var User&MockObject $user */
        $user = $this->createMock(User::class);
        $user->method("getId")->willReturn(1);
        $user->method("getEmail")->willReturn("valid@example.com");

        $this->verifyEmailHelperMock = $this->getMockBuilder(VerifyEmailHelperInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(["validateEmailConfirmationFromRequest"])
            ->onlyMethods(["generateSignature", "validateEmailConfirmation"])
            ->getMock();

        $this->verifyEmailHelperMock->expects($this->once())
            ->method("validateEmailConfirmationFromRequest")
            ->with($this->callback(function ($modifiedRequest) {
                return $modifiedRequest instanceof Request
                    && strpos($modifiedRequest->getUri(), ":9080") === false;
            }), "1", "valid@example.com");

        $this->securityService = new SecurityService($this->verifyEmailHelperMock, $this->validatorMock);
        $this->securityService->verifyEmailRequestSignature($request, $user);
    }
}
