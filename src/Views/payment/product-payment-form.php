<form method="post" class="product-order-form">

    <div class="mb-3">
        <input type="text" name="customer_name" class="form-control" placeholder="Ваше имя" required>
    </div>

    <div class="mb-3">
        <input type="email" name="customer_email" class="form-control" placeholder="Ваш e-mail" required>
    </div>

    <input type="hidden" name="product_id"  value="<?php echo esc_attr($product_id); ?>">
    <?php wp_nonce_field('create_order', 'create_order_nonce'); ?>

    <button type="submit" name="create_order_submit" class="btn btn-primary w-100">
        Купить за <?=$price??0?> &#8381;
    </button>
</form>