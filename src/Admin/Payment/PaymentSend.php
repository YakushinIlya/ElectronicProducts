<?php

namespace ElectronicProducts\Admin\Payment;

defined('ABSPATH') || exit;

class PaymentSend
{
    public static function handle_order_form(): void
    {

        if ( !isset($_POST['create_order_submit']) ) {
            return;
        }

        if ( !isset($_POST['create_order_nonce']) || !wp_verify_nonce($_POST['create_order_nonce'], 'create_order') ) {
            wp_die('Ошибка безопасности.');
        }

        $name  = sanitize_text_field($_POST['customer_name'] ?? '');
        $email = sanitize_email($_POST['customer_email'] ?? '');
        $product_id = (int) ($_POST['product_id'] ?? 0);
        $price = (float) get_post_meta($product_id, '_product_price', true);;

        if (!$name || !$email || !$product_id || !$price) {
            wp_die('Некорректные данные.');
        }

        $order_id = self::create_order([
            'product_id' => $product_id,
            'name'       => $name,
            'email'      => $email,
            'total'      => $price,
        ]);

        if (!$order_id) {
            wp_die('Не удалось создать заказ.');
        }

        self::redirect_to_payment($order_id);
    }

    private static function redirect_to_payment($order_id): void
    {
        $settings = get_option('product_payment_settings', []);
        $method   = $settings['payment_method'] ?? null;

        switch ($method) {
            case'yookassa':
                PaymentYookassa::redirect_to_yookassa($order_id);
                break;
            case'manual':
                PaymentManual::redirect_to_manual($order_id);
                break;
            default:
                wp_die('Способ оплаты не выбран.');
                break;
        }
    }

    public static function register_webhook_endpoint(): void
    {
        add_rewrite_rule(
            '^yookassa-webhook/?$',
            'index.php?yookassa_webhook=1',
            'top'
        );

        add_filter('query_vars', function ($vars) {
            $vars[] = 'yookassa_webhook';
            return $vars;
        });
    }

    public static function handle_webhook(): void
    {

        if (get_query_var('yookassa_webhook') != 1) {
            return;
        }

        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        if (!$data || empty($data['event'])) {
            status_header(400);
            exit;
        }

        if ($data['event'] !== 'payment.succeeded') {
            status_header(200);
            exit;
        }

        $payment = $data['object'];
        $order_id = (int) ($payment['metadata']['order_id'] ?? 0);

        if (!$order_id) {
            status_header(400);
            exit;
        }

        global $wpdb;
        $table = $wpdb->prefix . EP_PLUGIN_ORDERS_TABLE;

        $order = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE id = %d",
                $order_id
            )
        );

        if (!$order) {
            status_header(404);
            exit;
        }

        if ( (float) $order->total !== (float) $payment['amount']['value'] ) {
            status_header(400);
            exit;
        }

        $token = wp_generate_password(32, false, false);

        $wpdb->update(
            $table,
            [
                'status' => 'paid',
                'download_token' => $token,
                'paid_at' => current_time('mysql'),
                'payment_id' => $payment['id'],
            ],
            ['id' => $order_id]
        );

        do_action('product_order_created', ['order'=>$order, 'token'=>$token]);

        status_header(200);
        exit;
    }

    private static function create_order($data): bool|int
    {
        global $wpdb;
        $table = $wpdb->prefix . EP_PLUGIN_ORDERS_TABLE;

        $wpdb->insert($table, [
            'order_number' => uniqid('ORD-'),
            'product_id'   => $data['product_id'],
            'name'         => $data['name'],
            'email'        => $data['email'],
            'total'        => $data['total'],
            'status'       => 'pending',
            'ip'           => $_SERVER['REMOTE_ADDR'],
            'created_at'   => current_time('mysql'),
        ]);

        return $wpdb->insert_id ?: false;
    }
}