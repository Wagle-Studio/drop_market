<?php

namespace App\Tests\E2E\Shop;

use App\Entity\User;
use Tests\E2E\AbstractE2ETest;

class ProductReadTest extends AbstractE2ETest
{
    /**
     * @group E2E_product
     */
    public function testProductReadPageAccessWithUnknownProduct(): void
    {
        $this->action->visit(self::PRODUCT_INVALID_PATH);

        $this->verify->currentUrlPathMatch(self::PRODUCT_INVALID_PATH);

        $this->verify->pageNotFound();
    }

    /**
     * @group E2E_product
     * @dataProvider productReadPageAccessUserProvider
     */
    public function testProductReadPageAccessBasedOnRole(string $role, bool $shouldAccess): void
    {
        $this->action->loginAs($role);

        $product = $this->repository->findRandomProduct();

        $randomProductUrl = $this->helper->buildPath(self::PRODUCT_PATH, [
            "product_slug" => $product->getSlug(),
        ]);

        $this->action->visit($randomProductUrl);

        if (!$shouldAccess) {
            $this->verify->currentUrlPathMatch($randomProductUrl);
            $this->verify->pageAccessDenied();
        } else {
            // TODO: test something else.
            $this->verify->currentUrlPathMatch($randomProductUrl);
        }
    }

    /**
     * @group E2E_product
     * @return array<string, mixed>
     */
    public function productReadPageAccessUserProvider(): array
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
