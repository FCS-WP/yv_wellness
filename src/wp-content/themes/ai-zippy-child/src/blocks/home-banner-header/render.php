<?php
/**
 * Server-side render for ai-zippy/home-banner-header.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused — block has none).
 * @var WP_Block $block      Block instance.
 */

// Normalize broken <br> variants (handles raw HTML and entity-encoded forms
// such as </br>, < /br>, <br/>, &lt;/br&gt;, etc.) before sanitization.
$heading = (string) ($attributes['heading'] ?? '');
$heading = html_entity_decode($heading, ENT_QUOTES, 'UTF-8');
$heading = preg_replace('/<\s*\/?\s*br\s*\/?\s*>/i', '<br>', $heading);
$heading = wp_kses_post($heading);

$body = (string) ($attributes['body'] ?? '');
$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
$body = preg_replace('/<\s*\/?\s*br\s*\/?\s*>/i', '<br>', $body);
$body = wp_kses_post($body);
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
