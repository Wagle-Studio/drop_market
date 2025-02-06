<?php

namespace App\DataFixtures\Test;

use App\DataFixtures\AbstractFixtures;
use App\Entity\StatusWave;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class StatusWaveFixtures extends AbstractFixtures implements FixtureGroupInterface
{
    /**
     * @var array<array<string, mixed>>
     */
    public static array $status = [
        [
            "title" => "Brouillon",
            "const" => "DRAFT"
        ],
        [
            "title" => "Publié",
            "const" => "PUBLISHED"
        ],
        [
            "title" => "Ouvert",
            "const" => "REGISTRATION_OPEN"
        ],
        [
            "title" => "En cours de validation",
            "const" => "REGISTRATION_CLOSE"
        ],
        [
            "title" => "En cours",
            "const" => "LAUNCHED"
        ],
        [
            "title" => "Terminé",
            "const" => "CLOSE"
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        $this->createWaves($manager, self::$status);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ["test"];
    }

    /**
     * @param array<array<mixed, int|string>> $waves
     */
    private function createWaves(ObjectManager $manager, array $waves): void
    {
        for ($i = 0; $i < count($waves); $i++) {
            $wave = new StatusWave();
            $wave->setTitle($waves[$i]["title"]);
            $wave->setConst($waves[$i]["const"]);

            $manager->persist($wave);

            $this->setReference("status_wave_" . $i, $wave);
        }
    }
}
