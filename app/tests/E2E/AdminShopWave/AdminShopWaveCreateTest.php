<?php

namespace Tests\E2E\AdminShopWave;

use App\Entity\Wave;
use App\Entity\Shop;
use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class AdminShopWaveCreateTest extends AbstractE2ETest
{
    private const FORM_NAME = "wave_form";
    private const FORM_BUTTON_SAVE_AND_DRAFT_CONTENT = "Enregistrer en brouillon";
    private const FORM_BUTTON_SAVE_AND_PUBLISH_CONTENT = "Enregistrer et publier";

    /**
     * @group E2E_admin_shop_wave_create
     */
    public function testAdminShopWaveCreatePageAccessWithUnknownShop(): void
    {
        $this->action->loginAs(User::ROLE_SUPER_ADMIN);

        $this->action->visit(self::ADMIN_SHOP_WAVE_CREATE_INVALID_PATH);

        $this->verify->currentUrlPathMatch(self::ADMIN_SHOP_WAVE_CREATE_INVALID_PATH);

        $this->verify->pageNotFound();
    }

    /**
     * @group E2E_admin_shop_wave_create_and_draft
     * @dataProvider adminShopWaveCreateFormSubmitForRelatedShopUserProvider
     */
    public function testAdminShopWaveCreateAndDraftFormSubmitForRelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $shop = $this->repository->findUserRandomRelatedShop($role);

        $randomShopWaveCreateUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_CREATE_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->action->visit($randomShopWaveCreateUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopWaveCreateUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopWaveCreateFormAction($shop, self::FORM_BUTTON_SAVE_AND_DRAFT_CONTENT);
        }
    }

    /**
     * @group E2E_admin_shop_wave_create_and_publish
     * @dataProvider adminShopWaveCreateFormSubmitForRelatedShopUserProvider
     */
    public function testAdminShopWaveCreateAndPublishFormSubmitForRelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $shop = $this->repository->findUserRandomRelatedShop($role);

        $randomShopWaveCreateUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_CREATE_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->action->visit($randomShopWaveCreateUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopWaveCreateUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopWaveCreateFormAction($shop, self::FORM_BUTTON_SAVE_AND_PUBLISH_CONTENT);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function adminShopWaveCreateFormSubmitForRelatedShopUserProvider(): array
    {
        return [
            "Role Super Admin" => [User::ROLE_SUPER_ADMIN, true],
            "Role Admin" => [User::ROLE_ADMIN, true],
            "Role Owner" => [User::ROLE_OWNER, true],
            "Role Employee" => [User::ROLE_EMPLOYEE, false],
        ];
    }

    /**
     * @group E2E_admin_shop_wave_create_and_draft
     * @dataProvider adminShopWaveCreateFormSubmitForUnrelatedShopUserProvider
     */
    public function testAdminShopWaveCreateAndDraftFormSubmitForUnrelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $shop = $this->repository->findUserRandomUnrelatedShop($role);

        $randomShopWaveCreateUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_CREATE_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->action->visit($randomShopWaveCreateUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopWaveCreateUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopWaveCreateFormAction($shop, self::FORM_BUTTON_SAVE_AND_DRAFT_CONTENT);
        }
    }

    /**
     * @group E2E_admin_shop_wave_create_and_publish
     * @dataProvider adminShopWaveCreateFormSubmitForUnrelatedShopUserProvider
     */
    public function testAdminShopWaveCreateAndPublishFormSubmitForUnrelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $shop = $this->repository->findUserRandomUnrelatedShop($role);

        $randomShopWaveCreateUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_CREATE_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->action->visit($randomShopWaveCreateUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopWaveCreateUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopWaveCreateFormAction($shop, self::FORM_BUTTON_SAVE_AND_PUBLISH_CONTENT);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function adminShopWaveCreateFormSubmitForUnrelatedShopUserProvider(): array
    {
        return [
            "Role Super Admin" => [User::ROLE_SUPER_ADMIN, true],
            "Role Admin" => [User::ROLE_ADMIN, false],
            "Role Owner" => [User::ROLE_OWNER, false],
            "Role Employee" => [User::ROLE_EMPLOYEE, false],
        ];
    }

    private function shopWaveCreateFormAction(Shop $shop, string $formButtonContent): void
    {
        $this->verify->formExists(self::FORM_NAME);

        $waveStart = $this->helper->getDateInTheFuture();

        $formData = ["start" => $this->helper->formatDateForHtmlInput($waveStart)];

        $this->action->fillAndSubmitFormType(self::FORM_NAME, $formButtonContent, $formData);

        $expectedPathAfterSubmit = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->verify->currentUrlPathMatch($expectedPathAfterSubmit);

        $waveProperties = ["start" => $this->helper->formatDateForSql($waveStart)];
        $wave = $this->repository->findOneBy(Wave::class, $waveProperties);

        $this->verify->entityExist($wave);

        $this->verify->entitiesMatch($shop, $wave->getShop());

        if ($formButtonContent === self::FORM_BUTTON_SAVE_AND_DRAFT_CONTENT) {
            $this->verify->flashMessage("Créneau brouillon crée avec succès.");

            $this->assertEquals("DRAFT", $wave->getStatus()->getConst());
        }

        if ($formButtonContent === self::FORM_BUTTON_SAVE_AND_PUBLISH_CONTENT) {
            $this->verify->flashMessage("Créneau créé et publié avec succès");

            $this->assertEquals("PUBLISHED", $wave->getStatus()->getConst());
        }
    }
}
