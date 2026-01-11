<p>Здравствуйте, <?= esc_html( $data['order']->name ) ?>!<p>
<p>Оплата заказа № <?= esc_html( $data['order']->order_number ) ?> прошла успешно.<br>
    Файл товара по ссылке: <a href="<?= esc_html( $download_link ) ?>" target="_blank"><?= esc_html( $download_link ) ?></a><br>
Ссылка будет доступна 3 дня с момента оплаты.<br>
<p>Спасибо за покупку.</p>
<p>
    <a href='https://learn-top.ru'>
        <img src='https://learn-top.ru/wp-content/themes/LearnTop/assets/images/logo/logo.png'>
    </a>
</p>