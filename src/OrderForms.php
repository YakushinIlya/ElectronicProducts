<?php

namespace ElectronicProducts;

class OrderForms
{
    public static function get_order_form(int $product_id): string
    {

        $product = get_post($product_id);
        $price   = get_post_meta($product_id, '_product_price', true);

        if ( $product && $product->post_type === 'product' ) {
            ob_start();
            require_once EP_PLUGIN_DIR . 'src/Views/payment/product-payment-form.php';
            return ob_get_clean();
        }

        return '';
    }
}