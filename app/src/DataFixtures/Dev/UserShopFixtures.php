<?php

namespace App\DataFixtures\Dev;

use App\DataFixtures\AbstractFixtures;
use App\Entity\UserShop;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserShopFixtures extends AbstractFixtures implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $users = range(1, count(UserFixtures::$users));
        $fakeShops = array_map(fn($i) => ["random" => $i], range(1, ShopFixtures::SHOP_FIXTURES_POP));

        $this->createUserShop($manager, $users, ShopFixtures::$shops);
        $this->createUserShop($manager, $users, $fakeShops);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ShopFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ["dev"];
    }

    /**
     * @param array<int> $users
     * @param array<int|array, array<string, mixed>> $shops
     */
    private function createUserShop(ObjectManager $manager, array $users, array $shops): void
    {
        for ($i = 0; $i < count($users); $i++) {
            for ($o = 0; $o < count($shops); $o++) {
                $isRandomShop = isset($shops[$o]["random"]);

                $userShop = new UserShop();

                if (!$isRandomShop) {
                    $userShop->setShop($this->getReference("shop_{$o}"));
                    $userShop->setUser($this->getReference("user_{$i}"));
                } else {
                    $userShop->setShop($this->getReference("random_shop_{$o}"));
                    $userReference = "user_random_" . $this->faker->numberBetween(
                        0,
                        (UserFixtures::USER_FIXTURES_POP - 1)
                    );
                    $userShop->setUser($this->getReference($userReference));
                }

                $manager->persist($userShop);
            }

            if (isset($userShop)) {
                $this->setReference("user_shop_" . $i, $userShop);
            }
        }
    }
}
