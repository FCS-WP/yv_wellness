<?php
/**
 * Search Results Block — Server-side render.
 *
 * Renders the global search query results, grouped by post type:
 *   01 — Products (WooCommerce)
 *   02 — Blog Posts
 *   03 — Pages
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

defined('ABSPATH') || exit;

$search_query = get_search_query();
$per_section  = 6;

/**
 * Run a search query for a given post type.
 *
 * @return WP_Post[]
 */
$run_search = static function (string $post_type, string $term, int $limit): array {
    if ($term === '') {
        return [];
    }
    if ($post_type === 'product' && ! post_type_exists('product')) {
        return [];
    }

    $args = [
        'post_type'           => $post_type,
        'post_status'         => 'publish',
        's'                   => $term,
        'posts_per_page'      => $limit + 1, // fetch one extra to know if "view all" link is needed
        'ignore_sticky_posts' => true,
        'no_found_rows'       => false,
        'orderby'             => 'relevance',
    ];

    $query = new WP_Query($args);
    $posts = $query->posts;
    wp_reset_postdata();

    return is_array($posts) ? $posts : [];
};

$products_all = $run_search('product', $search_query, $per_section);
$posts_all    = $run_search('post',    $search_query, $per_section);
$pages_all    = $run_search('page',    $search_query, $per_section);

$products = array_slice($products_all, 0, $per_section);
$posts    = array_slice($posts_all,    0, $per_section);
$pages    = array_slice($pages_all,    0, $per_section);

$products_has_more = count($products_all) > $per_section;
$posts_has_more    = count($posts_all)    > $per_section;
$pages_has_more    = count($pages_all)    > $per_section;

$total_results = count($products) + count($posts) + count($pages);

/**
 * Build a URL that re-runs the search filtered by post type.
 */
$view_all_url = static function (string $post_type, string $term): string {
    return esc_url(add_query_arg([
        's'         => $term,
        'post_type' => $post_type,
    ], home_url('/')));
};

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'search-results',
]);

$section_index = 0;
?>
<section <?php echo $wrapper_attributes; ?> data-animate="fade-up">

    <?php if ($total_results === 0) : ?>
        <div class="search-results__empty" data-animate="fade-up">
            <div class="search-results__empty-ornament" aria-hidden="true">
                <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round">
                    <circle cx="34" cy="34" r="20"></circle>
                    <line x1="49" y1="49" x2="66" y2="66"></line>
                    <line x1="22" y1="34" x2="46" y2="34" stroke-dasharray="2 4"></line>
                </svg>
            </div>
            <span class="search-results__empty-kicker"><?php esc_html_e('No matches', 'ai-zippy-child'); ?></span>
            <h2 class="search-results__empty-title">
                <?php
                /* translators: %s: search term */
                printf(
                    esc_html__('Nothing found for %s', 'ai-zippy-child'),
                    '<em>&ldquo;' . esc_html($search_query) . '&rdquo;</em>'
                );
                ?>
            </h2>
            <p class="search-results__empty-desc">
                <?php esc_html_e('Try a different keyword, or browse our collections, journal entries, and pages below.', 'ai-zippy-child'); ?>
            </p>
            <div class="search-results__empty-form">
                <?php echo get_search_form(['echo' => false]); ?>
            </div>
        </div>

    <?php else : ?>

        <?php if (! empty($products)) :
            $section_index++;
        ?>
            <div class="search-results__section search-results__section--products" data-animate="fade-up">
                <header class="search-results__section-header">
                    <span class="search-results__section-index"><?php echo esc_html(sprintf('%02d', $section_index)); ?></span>
                    <h2 class="search-results__section-heading">
                        <?php esc_html_e('Products', 'ai-zippy-child'); ?>
                        <span class="search-results__count">(<?php echo count($products); ?>)</span>
                    </h2>
                    <span class="search-results__section-rule" aria-hidden="true"></span>
                </header>

                <div class="search-results__grid search-results__grid--products">
                    <?php foreach ($products as $product_post) :
                        $product = function_exists('wc_get_product') ? wc_get_product($product_post->ID) : null;
                        if ($product && ! $product->is_visible()) {
                            continue;
                        }
                        $permalink   = get_permalink($product_post->ID);
                        $title       = get_the_title($product_post->ID);
                        $thumb       = get_the_post_thumbnail_url($product_post->ID, 'woocommerce_thumbnail') ?: get_the_post_thumbnail_url($product_post->ID, 'medium');
                        $price_html  = $product ? $product->get_price_html() : '';
                        $cat_label   = '';
                        if ($product) {
                            $cats = wc_get_product_category_list($product_post->ID, ', ');
                            if ($cats) {
                                $cat_label = wp_strip_all_tags($cats);
                            }
                        }
                    ?>
                        <article class="search-results__card search-results__card--product" data-animate-child>
                            <a href="<?php echo esc_url($permalink); ?>" class="search-results__card-link">
                                <div class="search-results__card-image search-results__card-image--square">
                                    <?php if ($thumb) : ?>
                                        <img src="<?php echo esc_url($thumb); ?>"
                                             alt="<?php echo esc_attr($title); ?>"
                                             loading="lazy" />
                                    <?php else : ?>
                                        <div class="search-results__card-placeholder"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="search-results__card-body">
                                    <?php if ($cat_label) : ?>
                                        <span class="search-results__card-category"><?php echo esc_html($cat_label); ?></span>
                                    <?php endif; ?>
                                    <h3 class="search-results__card-title"><?php echo esc_html($title); ?></h3>
                                    <?php if ($price_html) : ?>
                                        <div class="search-results__card-price"><?php echo wp_kses_post($price_html); ?></div>
                                    <?php endif; ?>
                                    <span class="search-results__card-arrow" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="13 6 19 12 13 18"></polyline></svg>
                                    </span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($products_has_more) : ?>
                    <div class="search-results__section-footer">
                        <a class="search-results__view-all" href="<?php echo $view_all_url('product', $search_query); ?>">
                            <?php
                            /* translators: %s: search term */
                            printf(esc_html__('View all products for &ldquo;%s&rdquo;', 'ai-zippy-child'), esc_html($search_query));
                            ?>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (! empty($posts)) :
            $section_index++;
        ?>
            <div class="search-results__section search-results__section--posts" data-animate="fade-up">
                <header class="search-results__section-header">
                    <span class="search-results__section-index"><?php echo esc_html(sprintf('%02d', $section_index)); ?></span>
                    <h2 class="search-results__section-heading">
                        <?php esc_html_e('Blog Posts', 'ai-zippy-child'); ?>
                        <span class="search-results__count">(<?php echo count($posts); ?>)</span>
                    </h2>
                    <span class="search-results__section-rule" aria-hidden="true"></span>
                </header>

                <div class="search-results__grid search-results__grid--posts">
                    <?php foreach ($posts as $post_item) :
                        $permalink = get_permalink($post_item->ID);
                        $title     = get_the_title($post_item->ID);
                        $thumb     = get_the_post_thumbnail_url($post_item->ID, 'medium_large');
                        $excerpt   = wp_trim_words(get_the_excerpt($post_item->ID), 22, '&hellip;');
                        $date      = get_the_date('', $post_item->ID);
                        $iso_date  = get_the_date('c', $post_item->ID);
                    ?>
                        <article class="search-results__card search-results__card--post" data-animate-child>
                            <a href="<?php echo esc_url($permalink); ?>" class="search-results__card-link">
                                <div class="search-results__card-image search-results__card-image--landscape">
                                    <?php if ($thumb) : ?>
                                        <img src="<?php echo esc_url($thumb); ?>"
                                             alt="<?php echo esc_attr($title); ?>"
                                             loading="lazy" />
                                    <?php else : ?>
                                        <div class="search-results__card-placeholder"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="search-results__card-body">
                                    <time class="search-results__card-date" datetime="<?php echo esc_attr($iso_date); ?>"><?php echo esc_html($date); ?></time>
                                    <h3 class="search-results__card-title"><?php echo esc_html($title); ?></h3>
                                    <?php if ($excerpt) : ?>
                                        <p class="search-results__card-excerpt"><?php echo esc_html($excerpt); ?></p>
                                    <?php endif; ?>
                                    <span class="search-results__card-arrow" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="13 6 19 12 13 18"></polyline></svg>
                                    </span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($posts_has_more) : ?>
                    <div class="search-results__section-footer">
                        <a class="search-results__view-all" href="<?php echo $view_all_url('post', $search_query); ?>">
                            <?php
                            /* translators: %s: search term */
                            printf(esc_html__('View all blog posts for &ldquo;%s&rdquo;', 'ai-zippy-child'), esc_html($search_query));
                            ?>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (! empty($pages)) :
            $section_index++;
        ?>
            <div class="search-results__section search-results__section--pages" data-animate="fade-up">
                <header class="search-results__section-header">
                    <span class="search-results__section-index"><?php echo esc_html(sprintf('%02d', $section_index)); ?></span>
                    <h2 class="search-results__section-heading">
                        <?php esc_html_e('Pages', 'ai-zippy-child'); ?>
                        <span class="search-results__count">(<?php echo count($pages); ?>)</span>
                    </h2>
                    <span class="search-results__section-rule" aria-hidden="true"></span>
                </header>

                <div class="search-results__grid search-results__grid--pages">
                    <?php foreach ($pages as $page_item) :
                        $permalink = get_permalink($page_item->ID);
                        $title     = get_the_title($page_item->ID);
                        $excerpt   = wp_trim_words(get_the_excerpt($page_item->ID), 28, '&hellip;');
                    ?>
                        <article class="search-results__card search-results__card--page" data-animate-child>
                            <a href="<?php echo esc_url($permalink); ?>" class="search-results__card-link">
                                <div class="search-results__card-body">
                                    <span class="search-results__card-kicker"><?php esc_html_e('Page', 'ai-zippy-child'); ?></span>
                                    <h3 class="search-results__card-title"><?php echo esc_html($title); ?></h3>
                                    <?php if ($excerpt) : ?>
                                        <p class="search-results__card-excerpt"><?php echo esc_html($excerpt); ?></p>
                                    <?php endif; ?>
                                    <span class="search-results__card-arrow" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="13 6 19 12 13 18"></polyline></svg>
                                    </span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($pages_has_more) : ?>
                    <div class="search-results__section-footer">
                        <a class="search-results__view-all" href="<?php echo $view_all_url('page', $search_query); ?>">
                            <?php
                            /* translators: %s: search term */
                            printf(esc_html__('View all pages for &ldquo;%s&rdquo;', 'ai-zippy-child'), esc_html($search_query));
                            ?>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</section>
