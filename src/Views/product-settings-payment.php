<label>
    <input type="radio" name="product_payment_settings[payment_method]" value="yookassa"
        <?php checked($current, 'yookassa'); ?>>
    ЮKassa
</label><br>

<label>
    <input type="radio" name="product_payment_settings[payment_method]" value="manual"
        <?php checked($current, 'manual'); ?>>
    Ручная оплата
</label>