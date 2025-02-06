<?php

namespace App\Tests\E2E\Auth;

use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class LoginTest extends AbstractE2ETest
{
    /**
     * @group E2E_auth
     */
    public function testLoginSuccessfully(): void
    {
        $this->action->loginAs(User::ROLE_USER);

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->sessionCookieExists($this->helper->getCookiesName());
    }

    /**
     * @group E2E_auth
     */
    public function testLoginWithInvalidCredentials(): void
    {
        $this->action->loginAs("invalid_user");

        $this->assertSelectorTextContains(".form_grid__error", "Invalid credentials.");
    }
}
