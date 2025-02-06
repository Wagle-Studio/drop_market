<?php

namespace App\Tests\E2E\Auth;

use App\Entity\User;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Tests\E2E\AbstractE2ETest;

class VerifyUserEmailTest extends AbstractE2ETest
{
    /**
     * @group E2E_auth
     */
    public function testEmailVerificationSuccessfully(): void
    {
        $user = $this->userRegisterAction();

        /** @var VerifyEmailHelperInterface $verifyEmailHelper */
        $verifyEmailHelper = $this->getService(VerifyEmailHelperInterface::class);

        $signatureComponents = $verifyEmailHelper->generateSignature(
            "auth_email_verify",
            (string) $user->getId(),
            $user->getEmail()
        );

        $signedUrlWithPort = str_replace(
            "http://127.0.0.1",
            "http://127.0.0.1:9080",
            $signatureComponents->getSignedUrl()
        );

        $this->action->visit($signedUrlWithPort);

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->flashMessage("Votre adresse e-mail a été vérifiée.");

        $this->clearService("doctrine");

        $expectedVerifiedUser = $this->repository->find(User::class, $user->getId());
        $this->assertTrue($expectedVerifiedUser->isVerified());
    }

    /**
     * @group E2E_auth
     */
    public function testEmailVerificationWithInvalidSignature(): void
    {
        $user = $this->userRegisterAction();

        /** @var VerifyEmailHelperInterface $verifyEmailHelper */
        $verifyEmailHelper = $this->getService(VerifyEmailHelperInterface::class);

        $signatureComponents = $verifyEmailHelper->generateSignature(
            "auth_email_verify",
            (string) $user->getId(),
            $user->getEmail()
        );

        $invalidUrl = $signatureComponents->getSignedUrl() . "&signature=invalid_signature";

        $invalidUrlWithPort = str_replace(
            "http://127.0.0.1",
            "http://127.0.0.1:9080",
            $invalidUrl
        );

        $this->action->visit($invalidUrlWithPort);

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->flashMessage(
            "Nous avons rencontré un problème lors de la vérification de votre adresse e-mail."
        );

        $this->clearService("doctrine");

        $expectedVerifiedUser = $this->repository->find(User::class, $user->getId());
        $this->assertFalse($expectedVerifiedUser->isVerified());
    }

    private function userRegisterAction(): User
    {
        $email = $this->helper->getUniqueEmail();

        $this->action->registerAs($email, "User", "Test", "password123!");

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->flashMessage("Un email vous a été envoyé pour confirmer votre adresse email.");

        $userProperties = ["email" => $email];
        $user = $this->repository->findOneBy(User::class, $userProperties);

        $this->verify->entityExist($user);
        $this->assertFalse($user->isVerified());

        $this->verify->sessionCookieExists($this->helper->getCookiesName());

        return $user;
    }
}
