<?php

namespace Tests\E2E\Auth;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Tests\E2E\AbstractE2ETest;

class PasswordChangeTest extends AbstractE2ETest
{
    /**
     * @group E2E_auth_password
     */
    public function testChangePasswordSuccessfully(): void
    {
        $email = $this->helper->getUniqueEmail();
        $futurPassword = "!321drowssap";

        $passwordResetUrl = $this->generatePasswordResetTokenAction($email);

        $this->action->visit($passwordResetUrl);

        $this->verify->currentUrlPathMatch(self::AUTH_PASSWORD_PATH);

        $this->action->fillAndSubmitFormType(
            "password_change_form",
            "Sauvegarder le mot de passe",
            ["plainPassword" => $futurPassword, "plainPasswordConfirmation" => $futurPassword,]
        );

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->flashMessage(
            "Mot de passe mis à jour avec succès, veuillez vous connecter."
        );

        $this->action->loginAs("custom_user", $email, $futurPassword);

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->sessionCookieExists($this->helper->getCookiesName());
    }

    /**
     * @group E2E_auth_password
     */
    public function testChangePasswordPageLoadsWithInvalidToken(): void
    {
        $email = $this->helper->getUniqueEmail();

        $passwordResetUrl = $this->generatePasswordResetTokenAction($email);

        $invalidPasswordResetUrl = substr($passwordResetUrl, 0, -2) . "XX";

        $this->action->visit($invalidPasswordResetUrl);

        $this->verify->currentUrlPathMatch(self::AUTH_PASSWORD_RESET_PATH);

        $this->verify->flashMessage(
            "Nous avons rencontré un problème lors du changement de votre mot de passe."
        );
    }

    private function generatePasswordResetTokenAction(string $email): string
    {
        $this->action->registerAs($email, "User", "Test", "password123!");

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->sessionCookieExists($this->helper->getCookiesName());

        $userProperties = ["email" => $email];
        $user = $this->repository->findOneBy(User::class, $userProperties);

        /** @var ResetPasswordHelperInterface $resetEmailHelper */
        $resetEmailHelper = $this->getService(ResetPasswordHelperInterface::class);
        $resetToken = $resetEmailHelper->generateResetToken($user);

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getService(UrlGeneratorInterface::class);

        $validResetUrlWithInvalidPort = $urlGenerator->generate("auth_password_change", [
            "token" => $resetToken->getToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return str_replace(
            "http://127.0.0.1",
            "http://127.0.0.1:9080",
            $validResetUrlWithInvalidPort
        );
    }
}
