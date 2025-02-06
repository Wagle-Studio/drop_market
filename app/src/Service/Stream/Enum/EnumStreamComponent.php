<?php

namespace App\Service\Stream\Enum;

use App\Form\OrderFormType;
use App\Form\ShopFormType;
use App\Form\UserFormType;
use App\Form\WaveFormType;

enum EnumStreamComponent: string
{
    private const UI_FORM = "forms";
    private const UI_ATOM = "atoms";
    private const UI_MOLECULES = "molecules";
    private const UI_ORGANISMS = "organisms";

        // User.
    case HEADER_PROFILE = "header_profile";
    case CARD_PROFILE_READ = "card_profile_read";
    case CARD_PROFILE_EDIT = "card_profile_edit";

        // Shop.
    case WALLET_HEADER_ADMIN = "wallet_header_admin";
    case TABLE_ROW_SHOP = "table_row_shop";
    case CARD_ADMIN_SHOP_EDIT = "card_admin_shop_edit";

        // Wave.
    case TABLE_WAVE = "table_wave";
    case TABLE_ROW_WAVE = "table_row_wave";
    case CARD_ADMIN_SHOP_WAVE_READ = "card_admin_shop_wave_read";
    case CARD_ADMIN_SHOP_WAVE_EDIT = "card_admin_shop_wave_edit";

        // Order.
    case TABLE_ORDER = "table_order";
    case TABLE_ROW_ORDER = "table_row_order";
    case FORM_ORDER = "form_order";

    /**
     * @return array<string, string>
     */
    public function getConfiguration(): array
    {
        return match ($this) {
            // User.
            self::HEADER_PROFILE => ["type" => self::UI_MOLECULES],
            self::CARD_PROFILE_READ => ["type" => self::UI_ORGANISMS],

            // Shop.
            self::WALLET_HEADER_ADMIN => ["type" => self::UI_ORGANISMS],
            self::TABLE_ROW_SHOP => ["type" => self::UI_ATOM],

            // Wave.
            self::TABLE_WAVE => ["type" => self::UI_MOLECULES],
            self::TABLE_ROW_WAVE => ["type" => self::UI_ATOM],
            self::CARD_ADMIN_SHOP_WAVE_READ => ["type" => self::UI_ORGANISMS],

            // Order.
            self::TABLE_ORDER => ["type" => self::UI_MOLECULES],
            self::TABLE_ROW_ORDER => ["type" => self::UI_ATOM],

            // Form.
            self::CARD_PROFILE_EDIT => [
                "type"     => self::UI_ORGANISMS,
                "formType" => UserFormType::class,
                "formName" => "userForm"
            ],
            self::CARD_ADMIN_SHOP_EDIT => [
                "type"     => self::UI_ORGANISMS,
                "formType" => ShopFormType::class,
                "formName" => "shopForm"
            ],
            self::CARD_ADMIN_SHOP_WAVE_EDIT => [
                "type"     => self::UI_ORGANISMS,
                "formType" => WaveFormType::class,
                "formName" => "waveForm"
            ],
            self::FORM_ORDER => [
                "type"     => self::UI_FORM,
                "formType" => OrderFormType::class,
                "formName" => "orderForm"
            ]
        };
    }
}
