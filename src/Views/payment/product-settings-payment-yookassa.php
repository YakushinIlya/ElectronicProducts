<table class="form-table">
    <tr>
        <th>Shop ID</th>
        <td>
            <input type="text"
                   name="product_payment_settings[yookassa][shop_id]"
                   value="<?php echo esc_attr($yk['shop_id'] ?? ''); ?>"
                   class="regular-text">
        </td>
    </tr>

    <tr>
        <th>Секретный ключ</th>
        <td>
            <input type="password"
                   name="product_payment_settings[yookassa][secret_key]"
                   value="<?php echo esc_attr($yk['secret_key'] ?? ''); ?>"
                   class="regular-text">
        </td>
    </tr>

    <tr>
        <th>Страница успешной оплаты</th>
        <td>
            <?php
            $options = get_option('product_payment_settings', []);
            $success_page_id = (int) ($options['success_page_id'] ?? 0);
            wp_dropdown_pages([
                'name'              => 'product_payment_settings[success_page_id]',
                'selected'          => $success_page_id,
                'show_option_none'  => '— Выберите страницу —',
                'option_none_value' => 0,
            ]);
            ?>
            <p class="description">
                Пользователь будет перенаправлён на эту страницу после успешной оплаты
            </p>
        </td>
    </tr>

</table>