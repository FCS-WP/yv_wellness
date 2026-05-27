<?php
/**
 * About Us Information — server-side render
 *
 * @var array $attributes Block attributes.
 */

defined('ABSPATH') || exit;

if (!function_exists('ai_zippy_aui_inline_color')) {
    /**
     * Build a single inline CSS rule, or empty string when value is missing.
     */
    function ai_zippy_aui_inline_color($prop, $value)
    {
        if (empty($value)) {
            return '';
        }
        return $prop . ': ' . esc_attr($value) . ';';
    }
}

$sub_heading        = $attributes['subHeading'] ?? '';
$sub_heading_color  = $attributes['subHeadingColor'] ?? '#d50017';
$heading            = $attributes['heading'] ?? '';
$heading_color      = $attributes['headingColor'] ?? '#3B2715';
$description        = $attributes['description'] ?? '';
$description_color  = $attributes['descriptionColor'] ?? '#615245';
$image_url          = $attributes['imageUrl'] ?? '';
$image_alt          = $attributes['imageAlt'] ?? '';
$bg_color           = $attributes['bgColor'] ?? '#ffffff';
$bg_image_url       = $attributes['bgImageUrl'] ?? '';

$style_parts = [
    'background-color: ' . esc_attr($bg_color),
];

if (!empty($bg_image_url)) {
    $style_parts[] = 'background-image: url(' . esc_url($bg_image_url) . ')';
    $style_parts[] = 'background-size: cover';
    $style_parts[] = 'background-position: center';
}

$wrapper_classes = 'aui' . (!empty($bg_image_url) ? ' aui--has-bg' : '');

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => $wrapper_classes,
    'style' => implode('; ', $style_parts) . ';',
]);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if (!empty($bg_image_url)) : ?>
        <span class="aui__bg-overlay" aria-hidden="true"></span>
    <?php endif; ?>
    <div class="aui__wrapper">
        <div class="aui__content">
            <?php if (!empty($sub_heading)) : ?>
                <p
                    class="aui__sub-heading"
                    style="<?php echo esc_attr(ai_zippy_aui_inline_color('color', $sub_heading_color)); ?>"
                >
                    <?php echo esc_html(wp_strip_all_tags($sub_heading)); ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($heading)) : ?>
                <h2
                    class="aui__heading"
                    style="<?php echo esc_attr(ai_zippy_aui_inline_color('color', $heading_color)); ?>"
                >
                    <?php echo wp_kses_post($heading); ?>
                </h2>
            <?php endif; ?>

            <?php if (!empty($description)) : ?>
                <div
                    class="aui__description"
                    style="<?php echo esc_attr(ai_zippy_aui_inline_color('color', $description_color)); ?>"
                >
                    <?php echo wp_kses_post($description); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="aui__media">
            <div class="aui__media-frame<?php echo empty($image_url) ? ' aui__media-frame--empty' : ''; ?>">
                <span class="aui__media-stamp" aria-hidden="true"></span>
                <?php if (!empty($image_url)) : ?>
                    <img
                        class="aui__image"
                        src="<?php echo esc_url($image_url); ?>"
                        alt="<?php echo esc_attr($image_alt); ?>"
                        loading="lazy"
                    />
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
