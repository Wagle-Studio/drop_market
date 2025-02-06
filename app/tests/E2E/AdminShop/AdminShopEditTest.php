<?php

namespace App\Tests\E2E\AdminShop;

use App\Entity\Shop;
use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class AdminShopEditTest extends AbstractE2ETest
{
    private const FORM_NAME = "shop_form";
    private const FORM_BUTTON_CONTENT = "Enregistrer";

    /**
     * @group E2E_admin_shop_edit
     */
    public function testAdminShopEditPageAccessWithUnknownShop(): void
    {
        $this->action->loginAs(User::ROLE_SUPER_ADMIN);

        $this->action->visit(self::ADMIN_SHOP_EDIT_INVALID_PATH);

        $this->verify->currentUrlPathMatch(self::ADMIN_SHOP_EDIT_INVALID_PATH);

        $this->verify->pageNotFound();
    }

    /**
     * @group E2E_admin_shop_edit
     * @dataProvider adminShopEditFormSubmitForRelatedShopUserProvider
     */
    public function testAdminShopEditFormSubmitForRelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $shop = $this->repository->findUserRandomRelatedShop($role);

        $randomShopEditUrl = $this->helper->buildPath(self::ADMIN_SHOP_EDIT_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->action->visit($randomShopEditUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopEditUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopEditFormAction($shop);
        }
    }

    /**
     * @group E2E_admin_shop_edit
     * @return array<string, mixed>
     */
    public function adminShopEditFormSubmitForRelatedShopUserProvider(): array
    {
        return [
            "Role Super Admin" => [User::ROLE_SUPER_ADMIN, true],
            "Role Admin" => [User::ROLE_ADMIN, true],
            "Role Owner" => [User::ROLE_OWNER, true],
            "Role Employee" => [User::ROLE_EMPLOYEE, false],
        ];
    }

    /**
     * @group E2E_admin_shop_edit
     * @dataProvider adminShopEditFormSubmitForUnrelatedShopUserProvider
     */
    public function testAdminShopEditFormSubmitForUnrelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $shop = $this->repository->findUserRandomUnrelatedShop($role);

        $randomShopEditUrl = $this->helper->buildPath(self::ADMIN_SHOP_EDIT_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->action->visit($randomShopEditUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopEditUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopEditFormAction($shop);
        }
    }

    /**
     * @group E2E_admin_shop_edit
     * @return array<string, mixed>
     */
    public function adminShopEditFormSubmitForUnrelatedShopUserProvider(): array
    {
        return [
            "Role Super Admin" => [User::ROLE_SUPER_ADMIN, true],
            "Role Admin" => [User::ROLE_ADMIN, false],
            "Role Owner" => [User::ROLE_OWNER, false],
            "Role Employee" => [User::ROLE_EMPLOYEE, false],
        ];
    }

    /**
     * @param Shop $shop
     */
    private function shopEditFormAction($shop): void
    {
        $this->verify->formExists(self::FORM_NAME);

        $postalCode = strval(mt_rand(11111, 95500));

        $formData = ["postalCode" => $postalCode];
        $this->action->fillAndSubmitFormType(self::FORM_NAME, self::FORM_BUTTON_CONTENT, $formData);

        $expectedPathAfterSubmit = $this->helper->buildPath(self::ADMIN_SHOP_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->verify->currentUrlPathMatch($expectedPathAfterSubmit);

        $this->verify->flashMessage("Boutique mise à jour avec succès.");

        $this->clearService("doctrine");

        $shop = $this->repository->find(Shop::class, $shop->getId());

        $this->verify->entityExist($shop);

        $this->assertSame($postalCode, $shop->getPostalCode());
    }
}
