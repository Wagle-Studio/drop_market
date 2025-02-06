<?php

namespace Tests\E2E\_utils;

use App\Entity\Product;
use App\Entity\Wave;
use App\Entity\Shop;
use App\Entity\StatusWave;
use App\Entity\User;
use App\Entity\UserShop;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RepositoryHelper
{
    public function __construct(private ContainerInterface $containerInterface)
    {
    }

    private function getRepository(string $className): mixed
    {
        return $this->containerInterface->get("doctrine")->getRepository($className);
    }

    public function find(string $className, int $id): ?object
    {
        return $this->getRepository($className)->find($id);
    }

    /**
     * @param array<string, mixed> $properties
     * @return array<int, mixed>
     */
    public function findBy(string $className, array $properties): array
    {
        return $this->getRepository($className)->findBy($properties);
    }

    /**
     * @param array<string, mixed> $properties
     */
    public function findOneBy(string $className, array $properties): ?object
    {
        return $this->getRepository($className)->findOneBy($properties);
    }

    public function findUserRandomRelatedShop(
        string $role,
        bool $hasWave = false,
        StatusWave $statusWave = null
    ): ?Shop {
        $requiredUser = match ($role) {
            User::ROLE_SUPER_ADMIN => ["super_admin@wgls.fr", "123456"],
            User::ROLE_ADMIN => ["admin@wgls.fr", "123456"],
            User::ROLE_OWNER => ["owner@wgls.fr", "123456"],
            User::ROLE_EMPLOYEE => ["employee@wgls.fr", "123456"],
            default => null,
        };

        if ($requiredUser === null) {
            return null;
        }

        $user = $this->findOneBy(User::class, ["email" => $requiredUser[0]]);

        if (!$user) {
            return null;
        }

        $userShops = $this->findBy(UserShop::class, ["user" => $user->getId()]);

        if (empty($userShops)) {
            return null;
        }

        if ($hasWave && !isset($statusWave)) {
            $userShopsWithWaves = array_filter($userShops, function (UserShop $userShop) {
                $shopWaves = $this->findBy(Wave::class, ["shop" => $userShop->getShop()->getId()]);
                return !empty($shopWaves);
            });

            if (empty($userShopsWithWaves)) {
                return null;
            }

            return $userShopsWithWaves[array_rand($userShopsWithWaves)]->getShop();
        }

        if ($hasWave && $statusWave != null) {
            $userShopsWithWaves = array_filter($userShops, function (UserShop $userShop) use ($statusWave) {
                $shopWaves = $this->findBy(Wave::class, [
                    "shop" => $userShop->getShop()->getId(),
                    "status" => $statusWave->getId()
                ]);
                return !empty($shopWaves);
            });

            if (empty($userShopsWithWaves)) {
                return null;
            }

            return $userShopsWithWaves[array_rand($userShopsWithWaves)]->getShop();
        }

        return $userShops[array_rand($userShops)]->getShop();
    }

    public function findUserRandomUnrelatedShop(
        string $role,
        bool $hasWave = false,
        StatusWave $statusWave = null
    ): ?Shop {
        $requiredUser = match ($role) {
            User::ROLE_SUPER_ADMIN => ["super_admin@wgls.fr", "123456"],
            User::ROLE_ADMIN => ["admin@wgls.fr", "123456"],
            User::ROLE_OWNER => ["owner@wgls.fr", "123456"],
            User::ROLE_EMPLOYEE => ["employee@wgls.fr", "123456"],
            default => null,
        };

        if ($requiredUser === null) {
            return null;
        }

        $user = $this->findOneBy(User::class, ["email" => $requiredUser[0]]);
        if (!$user) {
            return null;
        }

        $relatedShops = $this->findBy(UserShop::class, ["user" => $user->getId()]);
        $relatedShopIds = array_map(fn(UserShop $userShop) => $userShop->getShop()->getId(), $relatedShops);

        $allShops = $this->findBy(Shop::class, []);

        $unrelatedShops = array_filter($allShops, fn(Shop $shop) => !in_array($shop->getId(), $relatedShopIds));

        if (empty($unrelatedShops)) {
            return null;
        }

        if ($hasWave && !isset($statusWave)) {
            $unrelatedShopsWithWaves = array_filter($unrelatedShops, function (Shop $shop) {
                $shopWaves = $this->findBy(Wave::class, ["shop" => $shop->getId()]);
                return !empty($shopWaves);
            });

            if (empty($unrelatedShopsWithWaves)) {
                return null;
            }

            return $unrelatedShopsWithWaves[array_rand($unrelatedShopsWithWaves)];
        }

        if ($hasWave && $statusWave != null) {
            $unrelatedShopsWithWaves = array_filter($unrelatedShops, function (Shop $shop) use ($statusWave) {
                $shopWaves = $this->findBy(Wave::class, [
                    "shop" => $shop->getId(),
                    "status" => $statusWave->getId()
                ]);
                return !empty($shopWaves);
            });

            if (empty($unrelatedShopsWithWaves)) {
                return null;
            }

            return $unrelatedShopsWithWaves[array_rand($unrelatedShopsWithWaves)];
        }


        return $unrelatedShops[array_rand($unrelatedShops)];
    }

    public function findRandomShopWave(Shop $shop, StatusWave $statusWave = null): ?Wave
    {
        $waveProperties = ["shop" => $shop->getId()];

        if ($statusWave) {
            $waveProperties["status"] = $statusWave->getId();
        }

        $waves = $this->findBy(Wave::class, $waveProperties);

        if (empty($waves)) {
            return null;
        }

        return $waves[array_rand($waves)];
    }

    public function findRandomProduct(): ?Product
    {
        $products = $this->findBy(Product::class, []);

        if (empty($products)) {
            return null;
        }

        return $products[array_rand($products)];
    }
}
