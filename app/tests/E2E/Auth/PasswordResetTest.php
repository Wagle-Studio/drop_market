<?php

namespace Tests\E2E\Auth;

use App\Entity\PasswordResetRequest;
use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class PasswordResetTest extends AbstractE2ETest
{
    /**
     * @group E2E_auth_password
     */
    public function testPasswordResetWithRegisteredUser(): void
    {
        $email = $this->helper->getUniqueEmail();

        $this->action->registerAs($email, "User", "Test", "password123!");

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->sessionCookieExists($this->helper->getCookiesName());

        $userProperties = ["email" => $email];
        $user = $this->repository->findOneBy(User::class, $userProperties);

        $this->verify->entityExist($user);

        $this->action->visit(self::AUTH_PASSWORD_RESET_PATH);

        $this->action->fillAndSubmitFormType(
            "password_reset_form",
            "Changer le mot de passe",
            ["email" => $email]
        );

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->flashMessage("Un email vous a été envoyé pour changer votre mot de passe.");

        $passwordResetRequestProperties = ["user" => $user->getId()];
        $passwordResetRequest = $this->repository->findOneBy(
            PasswordResetRequest::class,
            $passwordResetRequestProperties
        );

        $this->verify->entityExist($passwordResetRequest);
    }

    /**
     * @group E2E_auth_password
     */
    public function testPasswordResetWithUnknownUser(): void
    {
        $email = $this->helper->getUniqueEmail();

        $this->action->visit(self::AUTH_PASSWORD_RESET_PATH);

        $this->action->fillAndSubmitFormType(
            "password_reset_form",
            "Changer le mot de passe",
            ["email" => $email]
        );

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->flashMessage("Un email vous a été envoyé pour changer votre mot de passe.");
    }
}
