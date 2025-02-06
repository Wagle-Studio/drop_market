<?php

namespace Tests\E2E\AdminShopWave;

use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class AdminShopWaveCollectionTest extends AbstractE2ETest
{
    /**
     * @group E2E_admin_shop_wave_collection
     */
    public function testAdminShopWaveCollectionPageAccessWithUnknownShop(): void
    {
        $this->action->loginAs(User::ROLE_SUPER_ADMIN);

        $this->action->visit(self::ADMIN_SHOP_WAVE_INVALID_PATH);

        $this->verify->currentUrlPathMatch(self::ADMIN_SHOP_WAVE_INVALID_PATH);

        $this->verify->pageNotFound();
    }

    /**
     * @group E2E_admin_shop_wave_collection
     * @dataProvider adminShopWaveCollectionPageAccessForRelatedShopUserProvider
     */
    public function testAdminShopWaveCollectionPageAccessForRelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $shop = $this->repository->findUserRandomRelatedShop($role);

        $randomShopWaveUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->action->visit($randomShopWaveUrl);

        if ($shouldAccess) {
            // TODO: test something else.
            $this->verify->currentUrlPathMatch($randomShopWaveUrl);
        } else {
            $this->verify->currentUrlPathMatch($randomShopWaveUrl);
            $this->verify->pageAccessDenied();
        }
    }

    /**
     * @group E2E_admin_shop_wave_collection
     * @return array<string, mixed>
     */
    public function adminShopWaveCollectionPageAccessForRelatedShopUserProvider(): array
    {
        return [
            "Role Super Admin" => [User::ROLE_SUPER_ADMIN, true],
            "Role Admin" => [User::ROLE_ADMIN, true],
            "Role Owner" => [User::ROLE_OWNER, true],
            "Role Employee" => [User::ROLE_EMPLOYEE, true],
        ];
    }

    /**
     * @group E2E_admin_shop_wave_collection
     * @dataProvider adminShopWaveCollectionPageAccessForUnrelatedShopUserProvider
     */
    public function testAdminShopWaveCollectionPageAccessForUnrelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $shop = $this->repository->findUserRandomUnrelatedShop($role);

        $randomShopWaveUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->action->visit($randomShopWaveUrl);

        if ($shouldAccess) {
            // TODO: test something else.
            $this->verify->currentUrlPathMatch($randomShopWaveUrl);
        } else {
            $this->verify->currentUrlPathMatch($randomShopWaveUrl);
            $this->verify->pageAccessDenied();
        }
    }

    /**
     * @group E2E_admin_shop_wave_collection
     * @return array<string, mixed>
     */
    public function adminShopWaveCollectionPageAccessForUnrelatedShopUserProvider(): array
    {
        return [
            "Role Super Admin" => [User::ROLE_SUPER_ADMIN, true],
            "Role Admin" => [User::ROLE_ADMIN, false],
            "Role Owner" => [User::ROLE_OWNER, false],
            "Role Employee" => [User::ROLE_EMPLOYEE, false],
        ];
    }
}
