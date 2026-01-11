<?php

namespace ElectronicProducts\Admin;

defined('ABSPATH') || exit;

class Installer
{
    public static function init(): void
    {
        register_activation_hook(
            EP_PLUGIN_FILE,
            [self::class, 'activate']
        );
        register_deactivation_hook(
            EP_PLUGIN_FILE,
            [self::class, 'deactivation']
        );
        register_uninstall_hook(
            EP_PLUGIN_FILE,
            [self::class, 'uninstall']
        );
    }

    public static function activate(): void
    {
        global $wpdb;
        $table = $wpdb->prefix . EP_PLUGIN_ORDERS_TABLE;
        $charset = $wpdb->get_charset_collate();

        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$table}` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `order_number` varchar(255) NOT NULL,
        `payment_id` varchar(255) NOT NULL,
        `product_id` int(10) UNSIGNED NULL,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL,
        `total` DECIMAL(10,2) NOT NULL,
        `status` varchar(255) NOT NULL,
        `download_token` varchar(255) NOT NULL,
        `ip` varchar(255) NOT NULL,
        `paid_at` DATETIME NULL,
        `created_at` DATETIME NOT NULL,
        PRIMARY KEY (`id`)
        ) {$charset};");

    }

    public static function deactivation(): void
    {
        delete_option('product_settings');
        delete_post_meta_by_key('_product_price');
        delete_post_meta_by_key('_product_link');
        flush_rewrite_rules();
    }

    public static function uninstall(): void
    {
        self::deactivation();
        global $wpdb;
        $table = $wpdb->prefix . EP_PLUGIN_ORDERS_TABLE;
        $wpdb->query("DROP TABLE IF EXISTS `{$table}`");
    }
}
