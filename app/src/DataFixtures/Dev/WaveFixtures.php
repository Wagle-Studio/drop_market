<?php

namespace App\DataFixtures\Dev;

use App\DataFixtures\AbstractFixtures;
use App\Entity\Wave;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WaveFixtures extends AbstractFixtures implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createWave(
            $manager,
            array_map(
                fn($i) => [$i],
                range(1, (ShopFixtures::SHOP_FIXTURES_POP - 1))
            )
        );

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ShopFixtures::class,
            StatusWaveFixtures::class
        ];
    }

    public static function getGroups(): array
    {
        return ["dev"];
    }

    /**
     * @param array<array<int|string, int|string>> $shops
     */
    private function createWave(ObjectManager $manager, array $shops): void
    {
        for ($i = 0; $i < count($shops); $i++) {
            for ($o = 0; $o < count(StatusWaveFixtures::$status); $o++) {
                $wave = new Wave();
                $wave->setStart($this->faker->dateTimeBetween("-7 days", "now", "Europe/Paris"));
                $wave->setStatus($this->getReference("status_wave_{$o}"));
                $wave->setShop($this->getReference("random_shop_" . $shops[$i][0]));

                $manager->persist($wave);

                $this->setReference("wave_" . $o, $wave);
            }
        }
    }
}
