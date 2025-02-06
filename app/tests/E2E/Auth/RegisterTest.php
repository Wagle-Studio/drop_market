<?php

namespace App\Tests\E2E\Auth;

use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class RegisterTest extends AbstractE2ETest
{
    /**
     * @group E2E_auth
     */
    public function testRegistrationSuccessfully(): void
    {
        $email = $this->helper->getUniqueEmail();

        $this->action->registerAs($email, "User", "Test", "password123!");

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->flashMessage("Un email vous a été envoyé pour confirmer votre adresse email.");

        $userProperties = ["email" => $email];
        $user = $this->repository->findOneBy(User::class, $userProperties);

        $this->verify->entityExist($user);

        $this->verify->sessionCookieExists($this->helper->getCookiesName());
    }

    /**
     * @group E2E_auth
     */
    public function testRegistrationWithAlreadyRegisteredEmail(): void
    {
        $this->action->registerAs("super_admin@wgls.fr", "Super Admin", "Test", "password123!");

        $this->verify->pageElementExists("Il existe déjà un compte avec cette adresse email.");
    }

    /**
     * @group E2E_auth
     */
    public function testRegistrationWithMismatchedPasswords(): void
    {
        $this->action->visit(self::AUTH_REGISTER_PATH);

        $this->action->fillAndSubmitFormType("register_form", "Inscription", [
            "email" => $this->helper->getUniqueEmail(),
            "lastname" => "Test",
            "firstname" => "User",
            "plainPassword" => "password123",
            "plainPasswordConfirmation" => "321drowssap",
            "agreeTerms" => "1"
        ]);

        $this->verify->pageElementExists("Les mots de passe ne correspondent pas.");
    }
}
