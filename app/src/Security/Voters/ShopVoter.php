<?php

namespace App\Security\Voters;

use App\Entity\Shop;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Votes base on user role and relation with requisted shop(s).
 *
 * @extends Voter<string, Shop>
 */
class ShopVoter extends Voter
{
    public const READ_SHOP = "read_shop";
    public const EDIT_SHOP = "edit_shop";
    public const DELETE_SHOP = "delete_shop";

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
        User::ROLE_EMPLOYEE
    ];

    /**
     * Roles with edit access.
     *
     * @var array<string> $editAccess
     */
    public static array $editAccess = [
        User::ROLE_ADMIN,
        User::ROLE_OWNER
    ];

    /**
     * Roles with delete access.
     *
     * @var array<string> $deleteAccess
     */
    public static array $deleteAccess = [];

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (
            $attribute === self::READ_SHOP ||
            $attribute === self::EDIT_SHOP ||
            $attribute === self::DELETE_SHOP
        ) && $subject instanceof Shop;
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
            self::READ_SHOP => self::$readAccess,
            self::EDIT_SHOP => self::$editAccess,
            self::DELETE_SHOP => self::$deleteAccess,
            default => [],
        };

        if (empty(array_intersect($user->getRoles(), $requiredRoles))) {
            return false;
        }

        /** @var Shop $shop */
        $shop = $subject;

        return in_array($shop, $user->getShops());
    }
}
