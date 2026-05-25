<?php
/**
 * Server-side render for ai-zippy/home-banner-header.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused — block has none).
 * @var WP_Block $block      Block instance.
 */

$heading         = wp_kses_post($attributes['heading'] ?? '');
$body            = wp_kses_post($attributes['body'] ?? '');
$button_text     = esc_html($attributes['buttonText'] ?? 'SHOP NOW');
$button_url      = esc_url($attributes['buttonUrl'] ?? '#');
$bg_image_url    = esc_url($attributes['bgImageUrl'] ?? '');
$bg_color        = esc_attr($attributes['bgColor'] ?? '#FFFAF3');
$overlay_color   = esc_attr($attributes['overlayColor'] ?? 'rgba(255, 250, 243, 0.6)');
$overlay_opacity = intval($attributes['overlayOpacity'] ?? 60);

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'hbh',
    'style' => sprintf(
        'background-color: %s;%s',
        $bg_color,
        $bg_image_url ? sprintf(' background-image: url(%s);', $bg_image_url) : ''
    ),
]);
?>
<div <?php echo $wrapper_attributes; ?>>
    <div
        class="hbh__overlay"
        style="opacity: <?php echo esc_attr($overlay_opacity / 100); ?>; background-color: <?php echo $overlay_color; ?>;"
    ></div>
    <div class="hbh__container">
        <div class="hbh__content">
            <?php if ($heading) : ?>
                <h1 class="hbh__heading"><?php echo $heading; ?></h1>
            <?php endif; ?>
            <?php if ($body) : ?>
                <p class="hbh__body"><?php echo $body; ?></p>
            <?php endif; ?>
            <?php if ($button_text) : ?>
                <a href="<?php echo $button_url; ?>" class="hbh__btn"><?php echo $button_text; ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>
