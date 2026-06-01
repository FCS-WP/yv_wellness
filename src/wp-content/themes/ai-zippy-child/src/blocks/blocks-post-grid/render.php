<?php
/**
 * Blocks Post Grid — Server-side render.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks content.
 * @var WP_Block $block      Block instance.
 */

$columns         = isset($attributes['columns']) ? (int) $attributes['columns'] : 3;
$posts_per_page  = isset($attributes['postsPerPage']) ? (int) $attributes['postsPerPage'] : 6;
$order_by_attr   = isset($attributes['orderBy']) ? (string) $attributes['orderBy'] : 'date';
$order_attr      = isset($attributes['order']) ? (string) $attributes['order'] : 'DESC';
$categories      = isset($attributes['categories']) && is_array($attributes['categories']) ? $attributes['categories'] : [];
$show_filter     = !empty($attributes['showFilter']);
$show_post_count = !empty($attributes['showPostCount']);
$excerpt_lines   = isset($attributes['excerptLines']) ? (int) $attributes['excerptLines'] : 3;
$bg_color        = isset($attributes['bgColor']) ? (string) $attributes['bgColor'] : '#FFFAF3';
$card_bg_color   = isset($attributes['cardBgColor']) ? (string) $attributes['cardBgColor'] : '#ffffff';

// Allow client-side filter to override defaults via URL params.
$allowed_orderby = ['date', 'title'];
$allowed_order   = ['ASC', 'DESC'];

if (isset($_GET['bpg_orderby'])) {
    $candidate = sanitize_key((string) $_GET['bpg_orderby']);
    if (in_array($candidate, $allowed_orderby, true)) {
        $order_by_attr = $candidate;
    }
}

if (isset($_GET['bpg_order'])) {
    $candidate = strtoupper(sanitize_key((string) $_GET['bpg_order']));
    if (in_array($candidate, $allowed_order, true)) {
        $order_attr = $candidate;
    }
}

// Build the WP_Query.
$query_args = [
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => max(1, $posts_per_page),
    'orderby'             => $order_by_attr,
    'order'               => $order_attr,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => false,
];

if (!empty($categories)) {
    $query_args['category__in'] = array_map('intval', $categories);
}

$query = new WP_Query($query_args);

$wrapper_attrs = get_block_wrapper_attributes([
    'class'        => 'bpg',
    'data-animate' => 'fade-up',
]);

$wrapper_style = sprintf(
    '--bpg-columns:%d;--bpg-bg:%s;--bpg-card-bg:%s;--bpg-excerpt-lines:%d;',
    max(1, $columns),
    esc_attr($bg_color),
    esc_attr($card_bg_color),
    max(1, $excerpt_lines)
);

// Map current state to the matching select option value.
$current_selection = $order_by_attr . '_' . $order_attr;
$sort_options      = [
    'date_DESC'  => __('Latest', 'ai-zippy-child'),
    'date_ASC'   => __('Oldest', 'ai-zippy-child'),
    'title_ASC'  => __('A → Z', 'ai-zippy-child'),
    'title_DESC' => __('Z → A', 'ai-zippy-child'),
];
?>
<section <?php echo $wrapper_attrs; ?> style="<?php echo esc_attr($wrapper_style); ?>">
    <?php if ($show_post_count || $show_filter) : ?>
        <div class="bpg__toolbar">
            <?php if ($show_post_count) : ?>
                <div class="bpg__count">
                    <?php
                    /* translators: %d: number of posts found. */
                    printf(
                        esc_html(_n('Showing %d post', 'Showing %d posts', (int) $query->found_posts, 'ai-zippy-child')),
                        (int) $query->found_posts
                    );
                    ?>
                </div>
            <?php else : ?>
                <div class="bpg__count" aria-hidden="true"></div>
            <?php endif; ?>

            <?php if ($show_filter) : ?>
                <div class="bpg__filter">
                    <label class="screen-reader-text" for="bpg-sort"><?php echo esc_html__('Sort posts', 'ai-zippy-child'); ?></label>
                    <select id="bpg-sort" class="bpg__select" data-bpg-sort>
                        <?php foreach ($sort_options as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($current_selection, $value); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($query->have_posts()) : ?>
        <div class="bpg__grid stagger-group" data-animate="fade-up">
            <?php while ($query->have_posts()) :
                $query->the_post();
                $post_id   = get_the_ID();
                $thumb_url = get_the_post_thumbnail_url($post_id, 'medium_large');
                $title     = get_the_title();
                $excerpt   = wp_trim_words(get_the_excerpt(), 20, '...');
                $permalink = get_permalink();
                $alt_text  = !empty($title) ? $title : __('Post thumbnail', 'ai-zippy-child');
            ?>
                <article class="bpg__card" data-animate-child>
                    <a class="bpg__card-link" href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>">
                        <div class="bpg__card-image">
                            <?php if ($thumb_url) : ?>
                                <img
                                    src="<?php echo esc_url($thumb_url); ?>"
                                    alt="<?php echo esc_attr($alt_text); ?>"
                                    loading="lazy"
                                />
                            <?php else : ?>
                                <div class="bpg__card-placeholder" aria-hidden="true"></div>
                            <?php endif; ?>
                        </div>
                        <div class="bpg__card-body">
                            <h3 class="bpg__card-title"><?php echo esc_html($title); ?></h3>
                            <?php if ($excerpt) : ?>
                                <p class="bpg__card-excerpt"><?php echo esc_html($excerpt); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <div class="bpg__empty">
            <?php echo esc_html__('No posts found.', 'ai-zippy-child'); ?>
        </div>
    <?php endif; ?>

    <?php if ($show_filter) : ?>
        <script>
        (function () {
            var root = document.currentScript && document.currentScript.parentElement;
            if (!root) return;
            var select = root.querySelector('[data-bpg-sort]');
            if (!select) return;

            select.addEventListener('change', function (event) {
                var value = String(event.target.value || '');
                var parts = value.split('_');
                if (parts.length !== 2) return;

                var orderby = parts[0];
                var order   = parts[1];

                try {
                    var url = new URL(window.location.href);
                    url.searchParams.set('bpg_orderby', orderby);
                    url.searchParams.set('bpg_order', order);
                    window.location.href = url.toString();
                } catch (err) {
                    var sep = window.location.search ? '&' : '?';
                    window.location.href = window.location.pathname + window.location.search + sep
                        + 'bpg_orderby=' + encodeURIComponent(orderby)
                        + '&bpg_order=' + encodeURIComponent(order);
                }
            });
        }());
        </script>
    <?php endif; ?>
</section>
