<?php

namespace App\Tests\E2E\AdminShop;

use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class AdminShopReadTest extends AbstractE2ETest
{
    /**
     * @group E2E_admin_shop_read
     */
    public function testAdminShopReadPageAccessWithUnknownShop(): void
    {
        $this->action->loginAs(User::ROLE_SUPER_ADMIN);

        $this->action->visit(self::ADMIN_SHOP_INVALID_PATH);

        $this->verify->currentUrlPathMatch(self::ADMIN_SHOP_INVALID_PATH);

        $this->verify->pageNotFound();
    }

    /**
     * @group E2E_admin_shop_read
     * @dataProvider adminShopReadPageAccessForRelatedShopUserProvider
     */
    public function testAdminShopReadPageAccessForRelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $shop = $this->repository->findUserRandomRelatedShop($role);

        $randomShopReadUrl = $this->helper->buildPath(self::ADMIN_SHOP_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->action->visit($randomShopReadUrl);

        if ($shouldAccess) {
            // TODO: test something else.
            $this->verify->currentUrlPathMatch($randomShopReadUrl);
        } else {
            $this->verify->currentUrlPathMatch($randomShopReadUrl);
            $this->verify->pageAccessDenied();
        }
    }

    /**
     * @group E2E_admin_shop_read
     * @return array<string, mixed>
     */
    public function adminShopReadPageAccessForRelatedShopUserProvider(): array
    {
        return [
            "Role Super Admin" => [User::ROLE_SUPER_ADMIN, true],
            "Role Admin" => [User::ROLE_ADMIN, true],
            "Role Owner" => [User::ROLE_OWNER, true],
            "Role Employee" => [User::ROLE_EMPLOYEE, true],
        ];
    }

    /**
     * @group E2E_admin_shop_read
     * @dataProvider adminShopReadPageAccessForUnrelatedShopUserProvider
     */
    public function testAdminShopReadPageAccessForUnrelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $shop = $this->repository->findUserRandomUnrelatedShop($role);

        $randomShopReadUrl = $this->helper->buildPath(self::ADMIN_SHOP_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->action->visit($randomShopReadUrl);

        if ($shouldAccess) {
            // TODO: test something else.
            $this->verify->currentUrlPathMatch($randomShopReadUrl);
        } else {
            $this->verify->currentUrlPathMatch($randomShopReadUrl);
            $this->verify->pageAccessDenied();
        }
    }

    /**
     * @group E2E_admin_shop_read
     * @return array<string, mixed>
     */
    public function adminShopReadPageAccessForUnrelatedShopUserProvider(): array
    {
        return [
            "Role Super Admin" => [User::ROLE_SUPER_ADMIN, true],
            "Role Admin" => [User::ROLE_ADMIN, false],
            "Role Owner" => [User::ROLE_OWNER, false],
            "Role Employee" => [User::ROLE_EMPLOYEE, false],
        ];
    }
}
