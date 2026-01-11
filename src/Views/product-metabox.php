<table class="form-table">
    <tr>
        <th><label for="product_price">Цена:</label></th>
        <td>
            <input type="number" step="0.01" name="product_price"
                   id="product_price"
                   value="<?php echo esc_attr($price); ?>" />
        </td>
    </tr>

    <tr>
        <th><label for="product_link">Ссылка:</label></th>
        <td>
            <input type="text" name="product_link"
                   id="product_link"
                   value="<?php echo esc_attr($link); ?>" />
        </td>
    </tr>
</table>