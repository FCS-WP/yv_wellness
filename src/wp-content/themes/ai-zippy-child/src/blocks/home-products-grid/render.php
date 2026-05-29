<?php
$title = esc_html($attributes['title'] ?? 'Top Rated');
$title_color = esc_attr($attributes['titleColor'] ?? '#3B2715');
$bg_color = esc_attr($attributes['bgColor'] ?? '#FFF2F2');
$bg_image_url = esc_url($attributes['bgImageUrl'] ?? '');
$product_ids = $attributes['productIds'] ?? [];
$columns = intval($attributes['columns'] ?? 4);
$rows = intval($attributes['rows'] ?? 2);
$order_by = sanitize_text_field($attributes['orderBy'] ?? 'popularity');
$per_page = $columns * $rows;

// Build background style
$bg_style = sprintf('background-color: %s;', $bg_color);
if ($bg_image_url) {
    $bg_style .= sprintf(' background-image: url(%s); background-size: cover; background-position: center;', $bg_image_url);
}

// Query products
$query_args = [
    'post_type' => 'product',
    'post_status' => 'publish',
    'posts_per_page' => $per_page,
    'meta_query' => [
        [
            'key' => '_stock_status',
            'value' => 'instock',
            'compare' => '=',
        ],
    ],
];

if (!empty($product_ids)) {
    $query_args['post__in'] = array_map('intval', $product_ids);
    $query_args['orderby'] = 'post__in';
} else {
    switch ($order_by) {
        case 'date':
            $query_args['orderby'] = 'date';
            $query_args['order'] = 'DESC';
            break;
        case 'rating':
            $query_args['meta_key'] = '_wc_average_rating';
            $query_args['orderby'] = 'meta_value_num';
            $query_args['order'] = 'DESC';
            break;
        case 'price':
            $query_args['meta_key'] = '_price';
            $query_args['orderby'] = 'meta_value_num';
            $query_args['order'] = 'ASC';
            break;
        case 'rand':
            $query_args['orderby'] = 'rand';
            break;
        case 'popularity':
        default:
            $query_args['meta_key'] = 'total_sales';
            $query_args['orderby'] = 'meta_value_num';
            $query_args['order'] = 'DESC';
            break;
    }
}

$products_query = new WP_Query($query_args);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'hpg',
    'style' => $bg_style,
    'data-animate' => 'fade-up',
]);
?>
<section <?php echo $wrapper_attributes; ?>>
  <?php if ($title) : ?>
    <h2 class="hpg__title" style="color: <?php echo $title_color; ?>"><?php echo $title; ?></h2>
  <?php endif; ?>

  <?php if ($products_query->have_posts()) : ?>
    <div class="hpg__grid" style="--hpg-columns: <?php echo $columns; ?>">
      <?php while ($products_query->have_posts()) : $products_query->the_post(); ?>
        <?php
        global $product;
        $product = wc_get_product(get_the_ID());
        if (!$product) continue;
        $product_url = get_permalink();
        $product_image = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
        $product_name = $product->get_name();
        $product_price = $product->get_price_html();
        ?>
        <a href="<?php echo esc_url($product_url); ?>" class="hpg__card hover-lift-sm hover-scale-sm">
          <div class="hpg__card-image">
            <?php if ($product_image) : ?>
              <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_name); ?>" loading="lazy" />
            <?php else : ?>
              <div class="hpg__card-placeholder"></div>
            <?php endif; ?>
          </div>
          <h3 class="hpg__card-name"><?php echo esc_html($product_name); ?></h3>
          <p class="hpg__card-price"><?php echo $product_price; ?></p>
        </a>
      <?php endwhile; ?>
    </div>
    <?php wp_reset_postdata(); ?>
  <?php else : ?>
    <p class="hpg__empty">No products found.</p>
  <?php endif; ?>
</section>
