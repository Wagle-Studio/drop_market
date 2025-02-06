<?php

namespace App\Tests\E2E\AdminShop;

use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class AdminShopWaveDeleteTest extends AbstractE2ETest
{
    /**
     * @group E2E_admin_shop_wave_delete
     */
    public function testAdminShopWaveDeletePageGetMethod(): void
    {
        $this->action->loginAs(User::ROLE_SUPER_ADMIN);

        $shop = $this->repository->findUserRandomRelatedShop(User::ROLE_SUPER_ADMIN, true);
        $wave = $this->repository->findRandomShopWave($shop,);

        $randomShopWaveDeleteUrl = $this->helper->buildPath(self::ADMIN_SHOP_WAVE_DELETE_PATH, [
            "shop_slug" => $shop->getSlug(),
            "wave_ulid" => strval($wave->getUlid())
        ]);

        $this->action->visit($randomShopWaveDeleteUrl);

        $this->verify->pageMethodNotAllowed();
    }
}
