<?php

namespace App\DataFixtures\Dev;

use App\DataFixtures\AbstractFixtures;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends AbstractFixtures implements FixtureGroupInterface
{
    public const USER_FIXTURES_POP = 10;

    /**
     * @var array<array<string, string>>
     */
    public static array $users = [
        [
            "email" => "super_admin@wgls.fr",
            "password" => "123456",
            "lastname" => "super admin",
            "firstname" => "demo",
            "roles" => "ROLE_SUPER_ADMIN"
        ],
        [
            "email" => "admin@wgls.fr",
            "password" => "123456",
            "lastname" => "admin",
            "firstname" => "demo",
            "roles" => "ROLE_ADMIN"
        ],
        [
            "email" => "owner@wgls.fr",
            "password" => "123456",
            "lastname" => "owner",
            "firstname" => "demo",
            "roles" => "ROLE_OWNER"
        ],
        [
            "email" => "employee@wgls.fr",
            "password" => "123456",
            "lastname" => "employee",
            "firstname" => "demo",
            "roles" => "ROLE_EMPLOYEE"
        ],
        [
            "email" => "user@wgls.fr",
            "password" => "123456",
            "lastname" => "user",
            "firstname" => "demo",
            "roles" => ""
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $this->createUsers($manager, self::$users);
        $this->createUsers(
            $manager,
            array_map(fn($i) => ["random" => $i], range(1, self::USER_FIXTURES_POP))
        );

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ["dev"];
    }

    /**
     * @param array<array<int|string, int|string>> $users
     */
    private function createUsers(ObjectManager $manager, array $users): void
    {
        for ($i = 0; $i < count($users); $i++) {
            $isRandomUser = isset($users[$i]["random"]);

            $user = new User();
            $user->setEmail($isRandomUser ? $this->faker->email() : $users[$i]["email"]);
            $user->setLastname($isRandomUser ? $this->faker->lastName() : $users[$i]["lastname"]);
            $user->setFirstname($isRandomUser ? $this->faker->lastName() : $users[$i]["firstname"]);
            $user->setVerified($this->faker->boolean());
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $isRandomUser ? "123456" : $users[$i]["password"]
                )
            );
            $user->setRoles($isRandomUser ? [] : [$users[$i]["roles"]]);

            $manager->persist($user);

            $this->setReference($isRandomUser ? "user_random_{$i}" : "user_{$i}", $user);
        }
    }
}
