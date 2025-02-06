<?php

namespace App\Tests\E2E\Auth;

use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class LogoutTest extends AbstractE2ETest
{
    /**
     * @group E2E_auth
     */
    public function testLogoutSuccessfully(): void
    {
        $this->action->loginAs(User::ROLE_USER);

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->action->visit(self::PROFILE_PATH);

        $this->verify->currentUrlPathMatch(self::PROFILE_PATH);

        $this->action->visit(self::AUTH_LOGOUT_PATH);

        $this->verify->currentUrlPathMatch(self::HOMEPAGE_PATH);

        $this->verify->sessionCookieDoesNotExists($this->helper->getCookiesName());
    }
}
