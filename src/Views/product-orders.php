<div class="wrap">
    <h1>Заказы</h1>
    <form method="post">
    <?php
    $ordersTable->prepare_items();
    $ordersTable->display();
    ?>
    </form>
</div>