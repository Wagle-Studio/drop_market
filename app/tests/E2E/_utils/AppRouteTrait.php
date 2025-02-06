<?php

namespace Tests\E2E\_utils;

trait AppRouteTrait
{
    /** APP */
    public const HOMEPAGE_PATH = "/";

    /** AUTH */
    public const AUTH_REGISTER_PATH = "/auth/inscription";
    public const AUTH_LOGIN_PATH = "/auth/connexion";
    public const AUTH_LOGOUT_PATH = "/auth/deconnexion";
    public const AUTH_PASSWORD_PATH = "/auth/mot-de-passe";
    public const AUTH_PASSWORD_RESET_PATH = "/auth/mot-de-passe/reset";

    /** ADMIN SHOP */
    public const ADMIN_SHOP_PATH = "/admin/shops/{shop_slug}";
    public const ADMIN_SHOP_INVALID_PATH = "/admin/shops/invalid-slug";

    /** ADMIN SHOP EDIT */
    public const ADMIN_SHOP_EDIT_PATH = "/admin/shops/{shop_slug}/edition";
    public const ADMIN_SHOP_EDIT_INVALID_PATH = "/admin/shops/invalid-slug/edition";

    /** ADMIN SHOP DELETE */
    public const ADMIN_SHOP_DELETE_PATH = "/admin/shops/{shop_slug}/suppression";

    /** ADMIN SHOP WAVE */
    public const ADMIN_SHOP_WAVE_PATH = "/admin/shops/{shop_slug}/creneaux";
    public const ADMIN_SHOP_WAVE_INVALID_PATH = "/admin/shops/invalid-slug/creneaux";

    /** ADMIN SHOP WAVE CREATE */
    public const ADMIN_SHOP_WAVE_CREATE_PATH = "/admin/shops/{shop_slug}/creneaux/creation";
    public const ADMIN_SHOP_WAVE_CREATE_INVALID_PATH = "/admin/shops/invalid-slug/creneaux/creation";

    /** ADMIN SHOP WAVE EDIT */
    public const ADMIN_SHOP_WAVE_EDIT_PATH = "/admin/shops/{shop_slug}/creneaux/{wave_ulid}/edition";
    public const ADMIN_SHOP_WAVE_EDIT_INVALID_PATH =
    "/admin/shops/invalid-slug/creneaux/invalid-wave-id/edition";

    /** ADMIN SHOP WAVE DELETE */
    public const ADMIN_SHOP_WAVE_DELETE_PATH = "/admin/shops/{shop_slug}/creneaux/{wave_ulid}/suppression";

    /** PRODUCT */
    public const PRODUCT_PATH = "/produits/{product_slug}";
    public const PRODUCT_INVALID_PATH = "/produits/invalid-slug";

    /** PROFIL */
    public const PROFILE_PATH = "/profil";
}
