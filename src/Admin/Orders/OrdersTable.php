<?php

namespace ElectronicProducts\Admin\Orders;

defined('ABSPATH') || exit;

if ( !class_exists('\WP_List_Table') ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class OrdersTable extends \WP_List_Table
{
    public function __construct() {
        parent::__construct([
            'singular' => 'order',
            'plural'   => 'orders',
            'ajax'     => false,
        ]);
    }

    public function get_columns(): array
    {
        return [
            'cb'           => '<input type="checkbox" />',
            'order_number' => 'Заказ',
            'email'        => 'Email',
            'total'        => 'Сумма &#8381;',
            'status'       => 'Статус',
            'ip'           => 'IP покупателя',
            'created_at'   => 'Дата',
        ];
    }

    protected function get_sortable_columns(): array
    {
        return [
            'order_number' => ['order_number', false],
            'total'        => ['total', false],
            'created_at'   => ['created_at', true],
        ];
    }

    protected function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="order_ids[]" value="%d" />',
            (int) $item['id']
        );
    }

    protected function column_order_number($item): string
    {
        $url = admin_url('edit.php?post_type=product&page=product-orders&action=view&order_id=' . $item['id']);

        return sprintf(
            '<a href="%s"><strong>%s</strong></a>',
            esc_url($url),
            esc_html($item['order_number'])
        );
    }

    protected function column_status($item): string
    {
        $statuses = [
            'pending'   => 'В ожидании',
            'paid'      => 'Оплачен',
            'cancelled' => 'Отменён',
        ];

        return esc_html($statuses[$item['status']] ?? $item['status']);
    }

    protected function column_default($item, $column_name) {
        return esc_html($item[$column_name] ?? '');
    }

    protected function get_bulk_actions(): array
    {
        return [
            'mark_paid'      => 'Отметить как оплаченные',
            'mark_cancelled' => 'Отменить',
        ];
    }

    public function render_single_order($order_id): void
    {
        global $wpdb;
        $table = $wpdb->prefix . EP_PLUGIN_ORDERS_TABLE;

        $order = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE id = %d",
                $order_id
            )
        );
        $status = $this->column_status(['status'=>$order->status]);
        $download_link = get_post_meta($order->product_id, '_product_link', true);

        require_once EP_PLUGIN_DIR . 'src/Views/product-order-view.php';
    }

    public function process_bulk_action(): void
    {
        if ( empty($_POST['order_ids']) || !is_array($_POST['order_ids']) ) {
            return;
        }

        if ( !current_user_can('manage_options') ) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . EP_PLUGIN_ORDERS_TABLE;

        $ids = array_map('intval', $_POST['order_ids']);
        $ids_sql = implode(',', $ids);

        if ($this->current_action() === 'mark_paid') {
            $wpdb->query("UPDATE {$table} SET status = 'paid' WHERE id IN ($ids_sql)");
        }

        if ($this->current_action() === 'mark_cancelled') {
            $wpdb->query("UPDATE {$table} SET status = 'cancelled' WHERE id IN ($ids_sql)");
        }
    }

    public function prepare_items(): void
    {
        global $wpdb;
        $table = $wpdb->prefix . EP_PLUGIN_ORDERS_TABLE;

        $this->process_bulk_action();

        $per_page = 20;

        $paged  = $this->get_pagenum();
        $offset = ($paged - 1) * $per_page;

        $orderby = $_GET['orderby'] ?? 'created_at';
        $order   = $_GET['order'] ?? 'DESC';

        $allowed_orderby = ['order_number', 'total', 'created_at'];
        if ( !in_array($orderby, $allowed_orderby, true) ) {
            $orderby = 'created_at';
        }

        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $this->items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table}
                 ORDER BY $orderby $order
                 LIMIT %d OFFSET %d",
                $per_page,
                $offset
            ),
            ARRAY_A
        );

        $total_items = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ]);

        $this->_column_headers = [
            $this->get_columns(),
            [],
            $this->get_sortable_columns(),
        ];
    }
}