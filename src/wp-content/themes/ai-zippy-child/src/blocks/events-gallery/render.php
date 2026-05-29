<?php
/**
 * Events Gallery Block — Server-side render.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks content.
 * @var WP_Block $block      Block instance.
 */

$items          = $attributes['items'] ?? [];
$columns        = $attributes['columns'] ?? 3;
$gap            = $attributes['gap'] ?? 10;
$overlay_color  = $attributes['overlayColor'] ?? 'rgba(0, 0, 0, 0.7)';
$overlay_text   = $attributes['overlayTextColor'] ?? '#ffffff';
$border_radius  = $attributes['borderRadius'] ?? 12;

if (empty($items)) {
    return;
}

$wrapper_attrs = get_block_wrapper_attributes([
    'class'        => 'eg',
    'data-animate' => 'fade-up',
]);

$grid_style = sprintf(
    '--eg-columns:%d;--eg-gap:%dpx;--eg-radius:%dpx;--eg-overlay:%s;--eg-overlay-text:%s',
    $columns,
    $gap,
    $border_radius,
    esc_attr($overlay_color),
    esc_attr($overlay_text)
);
?>
<section <?php echo $wrapper_attrs; ?>>
    <div class="eg__grid stagger-group" data-animate="fade-up" style="<?php echo esc_attr($grid_style); ?>">
        <?php foreach ($items as $index => $item) :
            $url     = $item['url'] ?? '';
            $alt     = $item['alt'] ?? '';
            $caption = $item['caption'] ?? '';
            $span    = $item['span'] ?? 'normal';
            $class   = 'eg__item' . ($span === 'large' ? ' eg__item--large' : '');

            if (empty($url)) continue;
        ?>
            <article class="<?php echo esc_attr($class); ?>" data-animate-child>
                <div class="eg__image-wrap">
                    <img
                        class="eg__image"
                        src="<?php echo esc_url($url); ?>"
                        alt="<?php echo esc_attr($alt); ?>"
                        loading="lazy"
                    />
                    <?php if ($caption) : ?>
                        <div class="eg__overlay">
                            <span class="eg__caption"><?php echo esc_html($caption); ?></span>
                        </div>
                    <?php else : ?>
                        <div class="eg__overlay"></div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
