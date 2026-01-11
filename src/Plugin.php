<?php

namespace ElectronicProducts;

use ElectronicProducts\Admin\Block\OrderFormBlock;
use ElectronicProducts\Admin\MailSMTP;
use ElectronicProducts\Admin\Menu;
use ElectronicProducts\Admin\Installer;
use ElectronicProducts\Admin\Metabox;
use ElectronicProducts\Admin\Payment\PaymentSend;
use ElectronicProducts\Admin\Settings\SettingsPage;

defined('ABSPATH') || exit;

final class Plugin
{
    public static function init(): void
    {
        self::autoload();

        Installer::init();

        add_action('plugins_loaded', [Menu::class, 'register']);
        add_action( 'admin_init', [SettingsPage::class, 'register_settings'] );
        add_action( 'add_meta_boxes', [Metabox::class, 'register_product_metabox'] );
        add_action( 'save_post_product', [Metabox::class, 'save_product_meta'] );
        add_action( 'phpmailer_init', [MailSMTP::class, 'settings_smtp'] );
        add_action( 'product_order_created', [MailSMTP::class, 'send_order_email'] );
        add_action( 'init', [PaymentSend::class, 'handle_order_form'] );
        add_action( 'init', [PaymentSend::class, 'register_webhook_endpoint'] );
        add_action( 'template_redirect', [PaymentSend::class, 'handle_webhook'] );
        add_action( 'template_redirect', [Download::class, 'download_file'] );
        add_action( 'init', [OrderFormBlock::class, 'register'] );
        add_shortcode( 'ep_order_form', [OrderFormBlock::class, 'shortcode'] );

    }

    private static function autoload(): void
    {
        spl_autoload_register(function ($class) {
            if ( strpos($class, __NAMESPACE__) !== 0 ) {
                return;
            }

            $path = EP_PLUGIN_DIR . 'src/' .
                str_replace('\\', '/', substr($class, strlen(__NAMESPACE__) + 1)) .
                '.php';

            if ( file_exists($path) ) {
                require_once $path;
            }
        });
    }
}
