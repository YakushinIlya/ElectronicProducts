<?php

namespace ElectronicProducts\Admin\Orders;

use ElectronicProducts\Admin\Orders\OrdersTable;

defined('ABSPATH') || exit;

class OrdersPage
{
    public static function render(): void
    {
        $action = $_GET['action'] ?? '';
        $id     = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;

        $ordersTable = new OrdersTable();
        if ( $action === 'view' && $id ) {
            $ordersTable->render_single_order($id);
        } else {
            require_once EP_PLUGIN_DIR . 'src/Views/product-orders.php';
        }
    }


}