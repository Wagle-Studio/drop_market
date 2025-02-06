<?php

namespace App\DataFixtures\Dev;

use App\DataFixtures\AbstractFixtures;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends AbstractFixtures implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $this->createProducts($manager, [["title" => "Dev", "price" => "777", "description" => "Dev"]]);
        $this->createProducts($manager, range(1, 30));

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ShopFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ["dev"];
    }

    /**
     * @param array<int|array<string, int|string>> $products
     */
    private function createProducts(ObjectManager $manager, array $products): void
    {
        foreach ($products as $index => $productData) {
            $product = new Product();

            if (is_int($productData)) {
                $product->setTitle($this->faker->text(20));
                $product->setPriceTtc((string) $this->faker->numberBetween(0, 999));

                if ($this->faker->boolean()) {
                    $product->setDescription($this->faker->text(250));
                }
            } elseif (is_array($productData)) {
                $product->setTitle($productData["title"] ?? $this->faker->text(20));
                $product->setPriceTtc((string) ($productData["price"] ?? $this->faker->numberBetween(0, 999)));

                if (isset($productData["description"])) {
                    $product->setDescription($productData["description"]);
                }
            }

            $shopReference = $this->getReference(
                "random_shop_" . $this->faker->numberBetween(0, (ShopFixtures::SHOP_FIXTURES_POP - 1))
            );
            $product->setShop($shopReference);

            $manager->persist($product);

            $this->setReference("product_" . $index, $product);
        }
    }
}
