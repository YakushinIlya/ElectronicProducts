<div class="wrap">
    <h1>Настройки</h1>
    <?php settings_errors(); ?>
    <h2 class="nav-tab-wrapper">
        <a href="<?= esc_url(self::settings_url('payment')) ?>"
           class="nav-tab <?= $active_tab === 'payment' ? 'nav-tab-active' : '' ?>">
            Оплата
        </a>

        <a href="<?= esc_url(self::settings_url('notifications')) ?>"
           class="nav-tab <?= $active_tab === 'notifications' ? 'nav-tab-active' : '' ?>">
            Уведомления
        </a>
    </h2>
    <form method="post" action="options.php">
        <?php
        if ($active_tab === 'payment') {
            settings_fields('product_payment_group');
            do_settings_sections('product-settings-payment');
        } else {
            settings_fields('product_notifications_group');
            do_settings_sections('product-settings-notifications');
        }
        submit_button();
        ?>
    </form>
</div>