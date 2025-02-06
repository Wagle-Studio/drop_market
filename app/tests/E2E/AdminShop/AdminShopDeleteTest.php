<?php

namespace App\Tests\E2E\AdminShop;

use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class AdminShopDeleteTest extends AbstractE2ETest
{
    /**
     * @group E2E_admin_shop_delete
     */
    public function testAdminShopDeletePageGetMethod(): void
    {
        $this->action->loginAs(User::ROLE_SUPER_ADMIN);

        $shop = $this->repository->findUserRandomRelatedShop(User::ROLE_SUPER_ADMIN);

        $randomShopDeleteUrl = $this->helper->buildPath(self::ADMIN_SHOP_DELETE_PATH, [
            "shop_slug" => $shop->getSlug(),
        ]);

        $this->action->visit($randomShopDeleteUrl);

        $this->verify->pageMethodNotAllowed();
    }
}
