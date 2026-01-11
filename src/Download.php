<?php

namespace ElectronicProducts;

defined('ABSPATH') || exit;

class Download
{
    public static function download_file(): void
    {
        if ( !isset($_GET['token']) ) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . EP_PLUGIN_ORDERS_TABLE;
        $token = sanitize_text_field($_GET['token']);

        $order = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE download_token = %s AND status = 'paid'",
            $token
        ));

        if ( !$order ) {
            wp_die('Неверная ссылка или заказ не найден.');
        }

        $paid_time = strtotime($order->paid_at);
        if ( time() - $paid_time > 3 * DAY_IN_SECONDS ) {
            wp_die('Срок действия ссылки истёк.');
        }

        $file_path = str_replace(home_url('/'), ABSPATH, get_post_meta($order->product_id, '_product_link', true));

        if ( !file_exists($file_path) ) {
            wp_die('Файл не найден.');
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    }
}