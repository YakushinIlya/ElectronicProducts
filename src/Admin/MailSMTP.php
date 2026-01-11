<?php

namespace ElectronicProducts\Admin;

defined('ABSPATH') || exit;

class MailSMTP
{
    public static function settings_smtp($phpmailer): void
    {
        $opts = get_option('product_notification_settings');
        $phpmailer->isSMTP();
        $phpmailer->Host       = $opts['smtp_host'];
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = $opts['smtp_port'];
        $phpmailer->Username   = $opts['smtp_user'];
        $phpmailer->Password   = $opts['smtp_pass'];
        $phpmailer->SMTPSecure = $opts['smtp_secure']??'tls';
        $phpmailer->From       = $opts['smtp_from'];
        $phpmailer->FromName   = $opts['smtp_from_name'];
    }

    public static function send_order_email($data): void
    {
        $download_link = add_query_arg('token', $data['token'], site_url('/download-product/'));

        $to = $data['order']->email;

        $subject = "{$data['order']->name}, ваш заказ №{$data['order']->order_number} оплачен";

        ob_start();
        require EP_PLUGIN_DIR . 'src/Views/product-send-mail.php';
        $message = ob_get_clean();

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
        ];

        if ( !wp_mail($to, $subject, $message, $headers) ) {
            error_log('Failed to send email to ' . $to);
        } else {
            error_log('Email sent successfully to ' . $to);
        }
    }
}