<?php

namespace ElectronicProducts\Admin\Block;

use ElectronicProducts\OrderForms;

class OrderFormBlock
{
    public static function register(): void
    {
        wp_register_script(
            'ep-order-form-block',
            EP_PLUGIN_URL . 'assets/js/order-form-block.js',
            ['wp-blocks', 'wp-element', 'wp-editor', 'wp-data'],
            '1.0',
            true
        );

        register_block_type('electronic-products/order-form', [
            'editor_script' => 'ep-order-form-block',
            'render_callback' => [self::class, 'render'],
        ]);
    }

    public static function render(array $attributes): string
    {
        $product_id = $attributes['productId'] ?? get_the_ID();

        return do_shortcode(
            sprintf('[ep_order_form product_id="%d"]', (int)$product_id)
        );
    }
    public static function shortcode($atts) {

        $atts = shortcode_atts([
            'product_id' => 0,
        ], $atts);

        $product_id = (int) $atts['product_id'];

        if (!$product_id) {
            return '<p>Не указан ID товара</p>';
        }

        return OrderForms::get_order_form($product_id);
    }

    public static function ep_add_tinymce_button($buttons)
    {
        $buttons[] = 'ep_order_form';

        return $buttons;
    }

    public static function ep_add_tinymce_plugin($plugins)
    {
        $plugins['ep_order_form'] = EP_PLUGIN_URL . 'assets/js/editor.js';

        return $plugins;
    }

    public static function ep_add_tinymce_id($settings) {
        global $post;

        if ($post) {
            $settings['post_id'] = $post->ID;
        }

        return $settings;
    }
}