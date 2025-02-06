<?php

namespace Tests\E2E\AdminShopWave;

use App\Entity\Wave;
use App\Entity\Shop;
use App\Entity\StatusWave;
use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class AdminShopWaveEditTest extends AbstractE2ETest
{
    private const FORM_NAME = "wave_form";
    private const FORM_BUTTON_SAVE_CONTENT = "Enregistrer";
    private const FORM_BUTTON_SAVE_AND_DRAFT_CONTENT = "Dépublier";
    private const FORM_BUTTON_SAVE_AND_PUBLISH_CONTENT = "Publier";

    /**
     * @group E2E_admin_shop_wave_edit
     */
    public function testAdminShopWaveEditPageAccessWithUnknownShop(): void
    {
        $this->action->loginAs(User::ROLE_SUPER_ADMIN);

        $this->action->visit(self::ADMIN_SHOP_WAVE_EDIT_INVALID_PATH);

        $this->verify->currentUrlPathMatch(self::ADMIN_SHOP_WAVE_EDIT_INVALID_PATH);

        $this->verify->pageNotFound();
    }

    /**
     * @group E2E_admin_shop_wave_edit
     * @dataProvider adminShopWaveEditFormSubmitForRelatedShopUserProvider
     */
    public function testAdminShopWaveEditFormSubmitForRelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $statusWave = $this->repository->findBy(StatusWave::class, ["const" => "PUBLISHED"])[0];
        $shop = $this->repository->findUserRandomRelatedShop($role, true, $statusWave);
        $wave = $this->repository->findRandomShopWave($shop, $statusWave);

        $randomShopWaveEditUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_EDIT_PATH, [
            "shop_slug" => $shop->getSlug(),
            "wave_ulid" => strval($wave->getUlid())
        ]);

        $this->action->visit($randomShopWaveEditUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopWaveEditUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopWaveEditFormAction($shop, $wave, self::FORM_BUTTON_SAVE_CONTENT);
        }
    }

    /**
     * @group E2E_admin_shop_wave_edit_and_draft
     * @dataProvider adminShopWaveEditFormSubmitForRelatedShopUserProvider
     */
    public function testAdminShopWaveEditAndDraftFormSubmitForRelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $statusWave = $this->repository->findBy(StatusWave::class, ["const" => "PUBLISHED"])[0];
        $shop = $this->repository->findUserRandomRelatedShop($role, true, $statusWave);
        $wave = $this->repository->findRandomShopWave($shop, $statusWave);

        $randomShopWaveEditUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_EDIT_PATH, [
            "shop_slug" => $shop->getSlug(),
            "wave_ulid" => strval($wave->getUlid())
        ]);

        $this->action->visit($randomShopWaveEditUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopWaveEditUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopWaveEditFormAction($shop, $wave, self::FORM_BUTTON_SAVE_AND_DRAFT_CONTENT);
        }
    }

    /**
     * @group E2E_admin_shop_wave_edit_and_publish
     * @dataProvider adminShopWaveEditFormSubmitForRelatedShopUserProvider
     */
    public function testAdminShopWaveEditAndPublishFormSubmitForRelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $statusWave = $this->repository->findBy(StatusWave::class, properties: ["const" => "DRAFT"])[0];
        $shop = $this->repository->findUserRandomRelatedShop($role, true, $statusWave);
        $wave = $this->repository->findRandomShopWave($shop, $statusWave);

        $randomShopWaveEditUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_EDIT_PATH, [
            "shop_slug" => $shop->getSlug(),
            "wave_ulid" => strval($wave->getUlid())
        ]);

        $this->action->visit($randomShopWaveEditUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopWaveEditUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopWaveEditFormAction($shop, $wave, self::FORM_BUTTON_SAVE_AND_PUBLISH_CONTENT);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function adminShopWaveEditFormSubmitForRelatedShopUserProvider(): array
    {
        return [
            "Role Super Admin" => [User::ROLE_SUPER_ADMIN, true],
            "Role Admin" => [User::ROLE_ADMIN, true],
            "Role Owner" => [User::ROLE_OWNER, true],
            "Role Employee" => [User::ROLE_EMPLOYEE, false],
        ];
    }

    /**
     * @group E2E_admin_shop_wave_edit
     * @dataProvider adminShopWaveEditFormSubmitForUnrelatedShopUserProvider
     */
    public function testAdminShopWaveEditFormSubmitForUnrelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $statusWave = $this->repository->findBy(StatusWave::class, ["const" => "PUBLISHED"])[0];
        $shop = $this->repository->findUserRandomUnrelatedShop($role, true, $statusWave);
        $wave = $this->repository->findRandomShopWave($shop, $statusWave);

        $randomShopWaveEditUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_EDIT_PATH, [
            "shop_slug" => $shop->getSlug(),
            "wave_ulid" => strval($wave->getUlid())
        ]);

        $this->action->visit($randomShopWaveEditUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopWaveEditUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopWaveEditFormAction($shop, $wave, self::FORM_BUTTON_SAVE_CONTENT);
        }
    }

    /**
     * @group E2E_admin_shop_wave_edit_and_draft
     * @dataProvider adminShopWaveEditFormSubmitForUnrelatedShopUserProvider
     */
    public function testAdminShopWaveEditAndDraftFormSubmitForUnrelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $statusWave = $this->repository->findBy(StatusWave::class, ["const" => "PUBLISHED"])[0];
        $shop = $this->repository->findUserRandomUnrelatedShop($role, true, $statusWave);
        $wave = $this->repository->findRandomShopWave($shop, $statusWave);

        $randomShopWaveEditUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_EDIT_PATH, [
            "shop_slug" => $shop->getSlug(),
            "wave_ulid" => strval($wave->getUlid())
        ]);

        $this->action->visit($randomShopWaveEditUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopWaveEditUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopWaveEditFormAction($shop, $wave, self::FORM_BUTTON_SAVE_AND_DRAFT_CONTENT);
        }
    }

    /**
     * @group E2E_admin_shop_wave_edit_and_publish
     * @dataProvider adminShopWaveEditFormSubmitForUnrelatedShopUserProvider
     */
    public function testAdminShopWaveEditAndPublishFormSubmitForUnrelatedShopBasedOnRole(
        string $role,
        bool $shouldAccess
    ): void {
        $this->action->loginAs($role);

        $statusWave = $this->repository->findBy(StatusWave::class, properties: ["const" => "DRAFT"])[0];
        $shop = $this->repository->findUserRandomUnrelatedShop($role, true, $statusWave);
        $wave = $this->repository->findRandomShopWave($shop, $statusWave);

        $randomShopWaveEditUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_EDIT_PATH, [
            "shop_slug" => $shop->getSlug(),
            "wave_ulid" => strval($wave->getUlid())
        ]);

        $this->action->visit($randomShopWaveEditUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomShopWaveEditUrl);
            $this->verify->pageAccessDenied();
        } else {
            $this->shopWaveEditFormAction($shop, $wave, self::FORM_BUTTON_SAVE_AND_PUBLISH_CONTENT);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function adminShopWaveEditFormSubmitForUnrelatedShopUserProvider(): array
    {
        return [
            "Role Super Admin" => [User::ROLE_SUPER_ADMIN, true],
            "Role Admin" => [User::ROLE_ADMIN, false],
            "Role Owner" => [User::ROLE_OWNER, false],
            "Role Employee" => [User::ROLE_EMPLOYEE, false],
        ];
    }

    private function shopWaveEditFormAction(Shop $shop, Wave $wave, string $formButtonContent): void
    {
        $this->verify->formExists(self::FORM_NAME);

        $initialWaveStatus = $wave->getStatus();

        $newWaveStart = $this->helper->getDateInTheFuture();

        $formData = ["start" => $this->helper->formatDateForHtmlInput($newWaveStart)];
        $this->action->fillAndSubmitFormType(self::FORM_NAME, $formButtonContent, $formData);

        $expectedPathAfterSubmit = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->verify->currentUrlPathMatch($expectedPathAfterSubmit);

        $this->clearService("doctrine");

        $wave = $this->repository->find(Wave::class, $wave->getId());

        $this->verify->entityExist($wave);

        $shop = $this->repository->find(Shop::class, $shop->getId());

        $this->verify->entityExist($shop);

        $this->verify->entitiesMatch($shop, $wave->getShop());

        if ($formButtonContent === self::FORM_BUTTON_SAVE_CONTENT) {
            $this->verify->flashMessage("Créneau mis à jour avec succès.");
        }

        if ($formButtonContent === self::FORM_BUTTON_SAVE_AND_DRAFT_CONTENT) {
            $this->verify->flashMessage("Créneau dépublié avec succès.");

            $this->assertNotSame($wave->getStatus(), $initialWaveStatus);
            $this->assertSame($wave->getStatus()->getConst(), "DRAFT");
        }

        if ($formButtonContent === self::FORM_BUTTON_SAVE_AND_PUBLISH_CONTENT) {
            $this->verify->flashMessage("Créneau publié avec succès.");

            $this->assertNotSame($wave->getStatus(), $initialWaveStatus);
            $this->assertSame($wave->getStatus()->getConst(), "PUBLISHED");
        }
    }
}
