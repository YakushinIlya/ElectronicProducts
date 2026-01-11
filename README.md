# Электронные товары v 1.4
Данный **плагин для WordPress**, разрабатывался для продажи электронных товаров. В частности, для продажи тем, шаблонов, плагинов, модулей и прочего для разных CMS и фреймворков.
Плагин разработан проектом [learn-top.ru](https://learn-top.ru).

## Что умеет плагин
После установки WordPress плагина вы получите новый раздел в боковом меню административной панели, под названием "Товары (e-prod)".\
В этом разделе будут такие пункты как: "Все товары", "Добавить товар", "Категории товара", "Заказы", "Настройки".\
Разберем по пунктам:

- Все товары.
  - Здесь будет список товаров, как обычных записей. 
- Добавить товар.
  - Здесь вы увидите обычное добавление записи + два поля "Цена" и "Ссылка".
- Категории товара.
  - Это самый обычный раздел рубрик WP, но не пересекающийся с остальными рубриками.
- Заказы.
  - В этом разделе вы увидите список заказов, их статус и данные покупателя. А также, возможен детальный просмотр каждого заказа.
- Настройки.
  - В настройках вы можете подключить способ оплаты товара через YooKassa или ручной способ оплаты. А также, добавить настройки SMTP отправки писем клиенту со ссылкой для скачивания товара (действует 3 дня с момента покупки).

## Вывод формы заказа
Форма заказа выводится в принципе в любом месте с помощью шорткода или PHP вызова класса и его статического метода.
- Вывод с помощью шорткода: ``` [ep_order_form product_id="71"] ```
- Вывод с помощью PHP кода: ``` \ElectronicProducts\OrderForms::get_order_form($postID); ```
- В старой панели редактора WP (tiny) и в новой Gutenberg есть кнопка "Форма заказа", нажав на которую шорткод появится в нужном месте автоматически.

Стандартная форма заказа появляется с двумя полями для ввода - это имя и e-mail. После заполнения этих полей, клиент переходит сразу на оплату указанную в настройках.\
Если способ оплаты указан YooKassa, то пользователь сразу попадает на оплату, после которой он получит автоматически письмо со ссылкой для скачивания товара.\
Если способ оплаты указан ручной, то пользователь попадет на выбранную в настройках страницу с вашей информацией. Например, с информацией на какую карту перевести сумму заказа. Но в таком случае товар вы будете отправлять клиенту самостоятельно.

## Вывод списка товаров
Для того чтоб список товаров выводился в определенном разделе, нужно создать php файл в вашей теме с названием "archive-products.php".
В самом начале файла где подключается ``` get_header(); ``` нужно вставить такое содержимое:
```php
<?php
/*
Template Name: Товары
*/
get_header();
$page = get_page_by_path('products');
$args = [
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
];
$query = new WP_Query($args);
```
Далее, нужно вывести привычным вам способом, с элементами вашей верстки сам список товаров:
```php
<div class="col-12 mb-5">
        <h1 class="h3"><?php echo esc_html($page->post_title??the_title()); ?></h1>
        <?php echo $page->post_content??the_content(); ?>
        <div class="row justify-content-center">
            <?php if( $query->have_posts() ) : ?>
            <?php
            // Start the loop.
            while ( $query->have_posts() ) : $query->the_post();?>
                <?php
                $terms = get_the_terms(get_the_ID(), 'product_category');
                if ($terms && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $categoryPostID   = $term->term_id;
                        $categoryPostName = $term->name;
                        $linkCategoryTag  = '<a href="'.get_term_link($term).'" class="btn btn-outline-primary font-weight-600 rounded-1 p-2">'.esc_html($term->name).'</a>';
                        break;
                    }
                } ?>
                <div class="col-12 col-sm-6 col-md-6 col-lg-4 mb-3">
                    <article itemid="<?php the_ID(); ?>" itemscope itemtype="http://schema.org/BlogPosting" class="learn-post bg-white p-3 shadow-sm rounded-1 h-100">
                        <meta itemprop="author" content="<?=get_the_author()?>">
                        <meta itemprop="datePublished" content="<?=get_the_date()?>">
                        <meta itemprop="dateModified" content="<?=the_modified_date()?>">
                        <meta itemprop="articleSection" content="<?=$categoryPostName?>">

                        <div class="learn-post--img mb-3">
                            <a href="<?=get_the_permalink()?>">
                                <?php
                                $product_id = get_the_ID();
                                $price = '<span class="btn btn-primary text-white font-weight-600 rounded-1 p-2 me-1">'.number_format((float)get_post_meta($product_id, '_product_price', true), 0, '', ' ') . ' ₽ </span>';
                                if(has_post_thumbnail()) {
                                    $thumbnail_attributes = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
                                    echo '<img src="'.$thumbnail_attributes[0].'" alt="'.get_the_title().'" class="learn-post--img_previous img-fluid img-thumbnail w-100" itemprop="image">';
                                }
                                ?>
                            </a>
                        </div>
                        <div class="learn-post__title mb-3">
                            <a href="<?=get_the_permalink()?>" class="text-decoration-none text-dark">
                                <h2 class="h5"><?=get_the_title()?></h2>
                            </a>
                        </div>
                        <div class="learn-post__info d-flex text-center">
                            <div class="learn-post__info-price_publish"><?=$price?></div>
                            <div class="learn-post__info-category_publish"><?=$linkCategoryTag?></div>
                        </div>
                    </article>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>
    </div>
```
Далее, в административной панели нужно создать страницу и ее шаблоном выбрать "Товары". Шаблон мы только что создавали, и указали его имя: ```Template Name: Товары```.

## Внешний вид плагина
Все довольно просто, разберется даже не опытный разработчик.

![https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-1.jpg](https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-1.jpg "Все товары")

![https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-2.png](https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-2.png "Добавить товар")

![https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-3.jpg](https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-3.jpg "Категории товара")

![https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-4.png](https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-4.png "Заказы")

![https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-5.png](https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-5.png "Детальный заказ")

![https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-6.png](https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-6.png "Настройка оплаты")

![https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-7.jpg](https://learn-top.ru/wp-content/uploads/electronic-products/screen-plugin-7.jpg "Настройка SMTP")