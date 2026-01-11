<?php

namespace ElectronicProducts\Admin;

defined('ABSPATH') || exit;

class Metabox
{
    public static function register_product_metabox(): void
    {
        add_meta_box(
            'product_data',
            'Данные товара',
            [self::class, 'render_product_metabox'],
            'product',
            'normal',
            'high'
        );
    }

    public static function render_product_metabox($post): void
    {
        wp_nonce_field('save_product_data', 'product_nonce');

        $price = get_post_meta($post->ID, '_product_price', true);
        $link  = get_post_meta($post->ID, '_product_link', true);

        require_once EP_PLUGIN_DIR . 'src/Views/product-metabox.php';
    }

    public static function save_product_meta($post_id): void
    {

        if ( !isset($_POST['product_nonce']) || !wp_verify_nonce($_POST['product_nonce'], 'save_product_data') ) {
            return;
        }

        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return;
        }

        if ( !current_user_can('edit_post', $post_id) ) {
            return;
        }

        if ( isset($_POST['product_price']) ) {
            update_post_meta($post_id, '_product_price', (float) $_POST['product_price']);
        }

        if ( isset($_POST['product_link']) ) {
            update_post_meta($post_id, '_product_link', sanitize_text_field($_POST['product_link']));
        }
    }
}