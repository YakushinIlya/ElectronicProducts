<?php

namespace ElectronicProducts\Admin;

use ElectronicProducts\Admin\Orders\OrdersPage;
use ElectronicProducts\Admin\Settings\SettingsPage;

defined('ABSPATH') || exit;

class Menu
{
    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'add']);
        add_action( 'init', [self::class, 'create_product'] );
        add_action( 'init', [self::class, 'create_taxonomy'] );
    }

    public static function add(): void
    {
        add_submenu_page(
            'edit.php?post_type=product',
            'Заказы',
            'Заказы',
            'manage_options',
            'product-orders',
            [OrdersPage::class, 'render']
        );

        add_submenu_page(
            'edit.php?post_type=product',
            'Настройки',
            'Настройки',
            'manage_options',
            'product-settings',
            [SettingsPage::class, 'render_settings_page']
        );
    }

    public static function create_product(): void
    {
        register_post_type('product', [
            'labels' => [
                'name'               => 'Товары (Electronic products)',
                'singular_name'      => 'Товар',
                'add_new'            => 'Добавить товар',
                'add_new_item'       => 'Добавить товар',
                'edit_item'          => 'Редактировать товар',
                'new_item'           => 'Новый товар',
                'all_items'          => 'Все товары',
                'search_items'       => 'Искать товар',
                'not_found'          => 'Товары не найдены.',
                'not_found_in_trash' => 'В корзине нет товара.',
                'menu_name'          => 'Товары (e-prod)',
            ],
            'public'        => true,
            'menu_position' => 2,
            'menu_icon'     => 'dashicons-schedule',
            'supports'      => ['title', 'editor', 'thumbnail'],
            'taxonomies'    => ['product_category'],
            'has_archive'   => true,
            'rewrite'      => ['slug' => 'products'],
            'show_in_menu'  => true,
        ]);
        flush_rewrite_rules();
    }

    public static function create_taxonomy(): void
    {
        register_taxonomy('product_category', ['product'], [
            'labels' => [
                'name'          => 'Категории товара',
                'singular_name' => 'Категория товара',
                'menu_name'     => 'Категории товара',
            ],
            'public'            => true,
            'hierarchical'      => true,
            'rewrite'           => ['slug' => 'product_category'],
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_menu' => 'edit.php?post_type=product',
        ]);
        flush_rewrite_rules();
    }
}
