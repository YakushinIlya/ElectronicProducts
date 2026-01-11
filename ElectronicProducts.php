<?php
/**
 * Plugin Name: Электронные товары
 * Description: Плагин дает возможность размещать электронные товары для продажи. Создавался для продаж тем, плагинов, шаблонов, модулей и т.д.
 *
 * Plugin URI:  https://learn-top.ru/products
 * Author URI:  https://learn-top.ru
 * Author:      IT Шаман
 *
 * Version:     1.4
 */

const EP_PLUGIN_FILE = __FILE__;
const EP_PLUGIN_ORDERS_TABLE = 'electronic_product_orders';
define('EP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EP_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once EP_PLUGIN_DIR . 'src/Plugin.php';

ElectronicProducts\Plugin::init();
