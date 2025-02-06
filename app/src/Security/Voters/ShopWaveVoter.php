<?php

namespace App\Security\Voters;

use App\Entity\Wave;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Votes base on user role and relation with related wave shop.
 *
 * @extends Voter<string, Wave>
 */
class ShopWaveVoter extends Voter
{
    public const READ_SHOP_WAVE = "read_shop_wave";
    public const CREATE_SHOP_WAVE = "create_shop_wave";
    public const EDIT_SHOP_WAVE = "edit_shop_wave";
    public const DELETE_SHOP_WAVE = "delete_shop_wave";

    /**
     * Roles with all access.
     *
     * @var array<string> $allAccess
     */
    public static array $allAccess = [
        User::ROLE_SUPER_ADMIN
    ];

    /**
     * Roles with read access.
     *
     * @var array<string> $readAccess
     */
    public static array $readAccess = [
        User::ROLE_ADMIN,
        User::ROLE_OWNER,
        User::ROLE_EMPLOYEE,
    ];

    /**
     * Roles with create access.
     *
     * @var array<string> $createAccess
     */
    public static array $createAccess = [
        User::ROLE_ADMIN,
        User::ROLE_OWNER,
    ];

    /**
     * Roles with edit access.
     *
     * @var array<string> $editAccess
     */
    public static array $editAccess = [
        User::ROLE_ADMIN,
        User::ROLE_OWNER,
    ];

    /**
     * Roles with delete access.
     *
     * @var array<string> $deleteAccess
     */
    public static array $deleteAccess = [
        User::ROLE_ADMIN,
        User::ROLE_OWNER,
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (
            $attribute === self::READ_SHOP_WAVE ||
            $attribute === self::CREATE_SHOP_WAVE ||
            $attribute === self::EDIT_SHOP_WAVE ||
            $attribute === self::DELETE_SHOP_WAVE
        ) && $subject instanceof Wave;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (!empty(array_intersect($user->getRoles(), self::$allAccess))) {
            return true;
        }

        $requiredRoles = match ($attribute) {
            self::READ_SHOP_WAVE => self::$readAccess,
            self::CREATE_SHOP_WAVE => self::$createAccess,
            self::EDIT_SHOP_WAVE => self::$editAccess,
            self::DELETE_SHOP_WAVE => self::$deleteAccess,
            default => [],
        };

        if (empty(array_intersect($user->getRoles(), $requiredRoles))) {
            return false;
        }

        /** @var Wave $wave */
        $wave = $subject;

        if (!$wave->getShop()) {
            return false;
        }

        return in_array($wave->getShop(), $user->getShops());
    }
}
