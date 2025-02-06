<?php

namespace App\Tests\E2E\Shop;

use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class ProfileReadTest extends AbstractE2ETest
{
    /**
     * @group E2E_profile
     * @dataProvider userRoleProvider
     */
    public function testProfileReadPageAccessBasedOnRole(string $role, bool $shouldAccess): void
    {
        $this->action->loginAs($role);

        $this->action->visit(self::PROFILE_PATH);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch(self::PROFILE_PATH);
            $this->verify->pageAccessDenied();
        } else {
            // TODO: test something else.
            $this->verify->currentUrlPathMatch(self::PROFILE_PATH);
        }
    }

    /**
     * @group E2E_profile
     * @return array<string, mixed>
     */
    public function userRoleProvider(): array
    {
        return [
            "Role Super Admin" => [User::ROLE_SUPER_ADMIN, true],
            "Role Admin" => [User::ROLE_ADMIN, true],
            "Role Owner" => [User::ROLE_OWNER, true],
            "Role Employee" => [User::ROLE_EMPLOYEE, true],
            "Role User" => [User::ROLE_USER, true],
        ];
    }
}
