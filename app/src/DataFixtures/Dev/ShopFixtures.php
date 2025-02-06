<?php

namespace App\DataFixtures\Dev;

use App\DataFixtures\AbstractFixtures;
use App\Entity\Shop;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ShopFixtures extends AbstractFixtures implements FixtureGroupInterface
{
    public const SHOP_FIXTURES_POP = 30;

    /**
     * @var array<array<string, string>>
     */
    public static array $shops = [
        [
            "title" => "Grenoble - centre ville",
            "postal_code" => "38000",
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $this->createShops($manager, self::$shops);
        $this->createShops(
            $manager,
            array_map(fn($i) => ["random" => $i], range(1, self::SHOP_FIXTURES_POP))
        );

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ["dev"];
    }

    /**
     * @param array<array<string, int|string>> $shops
     */
    private function createShops(ObjectManager $manager, array $shops): void
    {
        for ($i = 0; $i < count($shops); $i++) {
            $isRandomShop = isset($shops[$i]["random"]);

            $shop = new Shop();
            $shop->setTitle($isRandomShop ? $this->faker->text(20) : $shops[$i]["title"]);
            $shop->setPostalCode(
                $isRandomShop ?
                    strval($this->faker->numberBetween(11111, 95500)) :
                    $shops[$i]["postal_code"]
            );

            $manager->persist($shop);

            $this->setReference($isRandomShop ? "random_shop_{$i}" : "shop_{$i}", $shop);
        }
    }
}
