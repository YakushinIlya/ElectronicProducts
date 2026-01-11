<?php

namespace ElectronicProducts\Admin\Settings;

use ElectronicProducts\Admin\Block\OrderFormBlock;

defined('ABSPATH') || exit;

class SettingsPage
{
    public static function register_settings(): void
    {
        register_setting(
            'product_payment_group',
            'product_payment_settings'
        );

        register_setting(
            'product_notifications_group',
            'product_notification_settings'
        );

        add_settings_section(
            'payment_section',
            'Способы оплаты',
            '__return_false',
            'product-settings-payment'
        );

        add_settings_field(
            'payment_method',
            'Активный способ оплаты',
            [self::class, 'render_payment_method_field'],
            'product-settings-payment',
            'payment_section'
        );

        add_settings_field(
            'yookassa_settings',
            'ЮKassa',
            [self::class, 'render_yookassa_fields'],
            'product-settings-payment',
            'payment_section'
        );

        add_settings_field(
            'manual_settings',
            'Ручная оплата',
            [self::class, 'render_manual_fields'],
            'product-settings-payment',
            'payment_section',
            [
                'option'      => 'product_payment_settings',
                'key'         => 'manual',
                'type'        => 'page',
                'description' => 'Страница с инструкцией по ручной оплате',
            ]
        );

        add_settings_section(
            'smtp_section',
            'SMTP настройки',
            '__return_false',
            'product-settings-notifications'
        );

        add_settings_field(
            'smtp_host',
            'SMTP Host',
            [self::class, 'render_smtp_field'],
            'product-settings-notifications',
            'smtp_section',
            [
                'option'      => 'product_notification_settings',
                'key'         => 'smtp_host',
                'placeholder' => 'smtp.learn-top.ru',
            ]
        );

        add_settings_field(
            'smtp_port',
            'SMTP Port',
            [self::class, 'render_smtp_field'],
            'product-settings-notifications',
            'smtp_section',
            [
                'option' => 'product_notification_settings',
                'key'    => 'smtp_port',
                'type'   => 'number',
                'class'  => 'small-text',
            ]
        );

        add_settings_field(
            'smtp_secure',
            'SMTP соединение',
            [self::class, 'render_smtp_field'],
            'product-settings-notifications',
            'smtp_section',
            [
                'option'      => 'product_notification_settings',
                'key'         => 'smtp_secure',
                'type'        => 'radio',
                'options' => [
                    'ssl' => 'SSL',
                    'tls' => 'TLS',
                ],
            ]
        );

        add_settings_field(
            'smtp_user',
            'SMTP Логин',
            [self::class, 'render_smtp_field'],
            'product-settings-notifications',
            'smtp_section',
            [
                'option' => 'product_notification_settings',
                'key'    => 'smtp_user',
            ]
        );

        add_settings_field(
            'smtp_pass',
            'SMTP Пароль',
            [self::class, 'render_smtp_field'],
            'product-settings-notifications',
            'smtp_section',
            [
                'option' => 'product_notification_settings',
                'key'    => 'smtp_pass',
                'type'   => 'password',
            ]
        );

        add_settings_field(
            'smtp_from',
            'E-mail отправителя',
            [self::class, 'render_smtp_field'],
            'product-settings-notifications',
            'smtp_section',
            [
                'option'      => 'product_notification_settings',
                'key'         => 'smtp_from',
                'placeholder' => 'smtp@learn-top.ru',
            ]
        );

        add_settings_field(
            'smtp_from_name',
            'Имя отправителя',
            [self::class, 'render_smtp_field'],
            'product-settings-notifications',
            'smtp_section',
            [
                'option'      => 'product_notification_settings',
                'key'         => 'smtp_from_name',
                'placeholder' => 'John Smith',
            ]
        );

        add_filter('mce_buttons', [OrderFormBlock::class, 'ep_add_tinymce_button']);
        add_filter('mce_external_plugins', [OrderFormBlock::class, 'ep_add_tinymce_plugin']);
        add_filter('tiny_mce_before_init', [OrderFormBlock::class, 'ep_add_tinymce_id']);
    }

    public static function render_settings_page(): void
    {
        $active_tab = $_GET['tab'] ?? 'payment';
        require_once EP_PLUGIN_DIR . 'src/Views/product-settings.php';
    }

    public static function render_smtp_field(array $args): void
    {
        $option_name  = $args['option'];
        $key          = $args['key'];
        $type         = $args['type'] ?? 'text';
        $placeholder  = $args['placeholder'] ?? '';
        $class        = $args['class'] ?? 'regular-text';
        $optionsRadio = $args['options'] ?? [];

        $options = get_option($option_name);
        $value   = $options[$key] ?? '';

        if ($type === 'radio') {
            foreach ($optionsRadio as $radio_value => $label) {
                printf(
                    '<label style="display:block;margin-bottom:6px">
                    <input type="radio" name="%s[%s]" value="%s" %s>
                    %s
                </label>',
                    esc_attr($option_name),
                    esc_attr($key),
                    esc_attr($radio_value),
                    checked($value, $radio_value, false),
                    esc_html($label)
                );
            }

            return;
        }

        printf(
            '<input type="%s" name="%s[%s]" value="%s" class="%s" placeholder="%s">',
            esc_attr($type),
            esc_attr($option_name),
            esc_attr($key),
            esc_attr($value),
            esc_attr($class),
            esc_attr($placeholder)
        );
    }

    private static function settings_url(string $tab): string
    {
        return admin_url(
            'edit.php?post_type=product&page=product-settings&tab=' . $tab
        );
    }

    public static function render_payment_method_field(): void
    {
        $options = get_option('product_payment_settings');
        $current = $options['payment_method'] ?? 'yookassa';
        require_once EP_PLUGIN_DIR . 'src/Views/product-settings-payment.php';
    }

    public static function render_yookassa_fields(): void
    {
        $options = get_option('product_payment_settings');
        $yk = $options['yookassa'] ?? [];
        require_once EP_PLUGIN_DIR . 'src/Views/payment/product-settings-payment-yookassa.php';
    }

    public static function render_manual_fields(array $args): void
    {
        $option_name = $args['option'];
        $key         = $args['key'];
        $description = $args['description'] ?? false;

        $options = get_option('product_payment_settings');
        $value   = $options[$key] ?? '';

        wp_dropdown_pages([
            'name'              => "{$option_name}[{$key}]",
            'selected'          => (int) $value,
            'show_option_none'  => '— Выберите страницу —',
            'option_none_value' => '',
        ]);

        if ($description) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }

        return;
    }
}