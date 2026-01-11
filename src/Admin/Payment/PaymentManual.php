<?php

namespace ElectronicProducts\Admin\Payment;

use JetBrains\PhpStorm\NoReturn;

defined('ABSPATH') || exit;

class PaymentManual
{
    #[NoReturn]
    public static function redirect_to_manual($order_id): void
    {
        $settings = get_option('product_payment_settings');

        $page_url = get_permalink((int) $settings['manual']);
        wp_safe_redirect($page_url . '?order_id=' . $order_id);
        exit;
    }
}