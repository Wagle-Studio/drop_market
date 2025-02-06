<?php

namespace App\DataFixtures\Test;

use App\DataFixtures\AbstractFixtures;
use App\Entity\UserShop;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserShopFixtures extends AbstractFixtures implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createUserShop($manager, range(1, count(UserFixtures::$users)));

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
        return ["test"];
    }

    /**
     * @param array<int> $users
     */
    private function createUserShop(ObjectManager $manager, array $users): void
    {
        for ($i = 0; $i < count($users); $i++) {
            for ($o = 0; $o < ShopFixtures::SHOP_FIXTURES_POP; $o++) {
                $userShop = new UserShop();
                $userShop->setShop($this->getReference("shop_{$o}"));

                if ($this->faker->boolean()) {
                    $userShop->setUser($this->getReference("user_{$i}"));
                } else {
                    $userReference = "user_random_" . $this->faker->numberBetween(
                        0,
                        (UserFixtures::USER_FIXTURES_POP - 1)
                    );
                    $userShop->setUser($this->getReference($userReference));
                }

                $manager->persist($userShop);
            }

            $this->setReference("user_shop_" . $i, $userShop);
        }
    }
}
