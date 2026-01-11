<?php

namespace ElectronicProducts\Admin\Payment;

use JetBrains\PhpStorm\NoReturn;

defined('ABSPATH') || exit;

class PaymentYookassa
{
    #[NoReturn]
    public static function redirect_to_yookassa(int $order_id): void
    {
        global $wpdb;
        $table = $wpdb->prefix . EP_PLUGIN_ORDERS_TABLE;

        $order = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE id = %d",
                $order_id
            )
        );

        if (!$order) {
            wp_die('Заказ не найден');
        }

        $settings = get_option('product_payment_settings');
        $shop_id  = $settings['yookassa']['shop_id'];
        $secret   = $settings['yookassa']['secret_key'];
        $success_page_id = (int) ($settings['success_page_id'] ?? 0);

        $body = [
            'amount' => [
                'value' => number_format($order->total, 2, '.', ''),
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => ($success_page_id > 0) ? get_permalink($success_page_id) : home_url('/'),
            ],
            'capture' => true,
            'description' => 'Оплата заказа #' . $order->order_number,
            'metadata' => [
                'order_id' => $order_id,
            ],
        ];

        $response = wp_remote_post(
            'https://api.yookassa.ru/v3/payments',
            [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($shop_id . ':' . $secret),
                    'Content-Type'  => 'application/json',
                    'Idempotence-Key' => uniqid('', true),
                ],
                'body' => json_encode($body),
                'timeout' => 30,
            ]
        );

        if (is_wp_error($response)) {
            wp_die('Ошибка соединения с ЮKassa');
        }

        $result = json_decode(wp_remote_retrieve_body($response), true);

        if ( empty($result['confirmation']['confirmation_url']) ) {
            wp_die('Не удалось создать платёж');
        }

        $wpdb->update(
            $table,
            ['payment_id' => $result['id']],
            ['id' => $order_id]
        );

        wp_redirect($result['confirmation']['confirmation_url']);
        exit;
    }
}