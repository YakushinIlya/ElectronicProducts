<div class="wrap">
    <?php if (!$order) { ?>
        <div class="notice notice-error"><p>Заказ не найден</p></div>
    <?php } else { ?>
        <table class="wp-list-table widefat fixed striped table-view-list orders">
            <thead>
                <tr>
                    <th colspan="2">
                        <h1>Заказ #<?=(int)$order->id?></h1>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Номер заказа:</td>
                    <td><?= esc_html($order->order_number) ?></td>
                </tr>
                <tr>
                    <td>Имя покупателя:</td>
                    <td><?= esc_html($order->name) ?></td>
                </tr>
                <tr>
                    <td>E-mail:</td>
                    <td><?= esc_html($order->email) ?></td>
                </tr>
                <tr>
                    <td>Статус:</td>
                    <td><?= esc_html($status) ?></td>
                </tr>
                <tr>
                    <td>Сумма:</td>
                    <td><?= esc_html($order->total??0) ?> &#8381;</td>
                </tr>
                <tr>
                    <td>IP покупателя:</td>
                    <td><?= esc_html($order->ip) ?></td>
                </tr>
                <tr>
                    <td>Дата создания заказа:</td>
                    <td><?= esc_html($order->created_at) ?></td>
                </tr>
                <?php if($order->paid_at): ?>
                <tr>
                    <td>Дата оплаты заказа:</td>
                    <td><?= esc_html($order->paid_at) ?></td>
                </tr>
                <?php endif; ?>
                <?php if($order->payment_id): ?>
                <tr>
                    <td>ID платежа:</td>
                    <td><?= esc_html($order->payment_id) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Товар:</td>
                    <td>
                        <a href="<?= esc_html(get_permalink($order->product_id)) ?>" target="_blank"><?= esc_html(get_the_title($order->product_id)) ?></a>
                    </td>
                </tr>
                <tr>
                    <td>Ссылка на товар:</td>
                    <td>
                        <a href="<?= $download_link ?>" target="_blank"><?= esc_html('Скачать') ?></a>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href="<?=admin_url('edit.php?post_type=product&page=product-orders')?>" class="button">← Назад к списку заказов</a>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php } ?>
</div>