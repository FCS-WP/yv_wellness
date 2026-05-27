<?php
/**
 * About Icons Text — server-side render
 *
 * @var array $attributes Block attributes.
 */

defined('ABSPATH') || exit;

if (!function_exists('ai_zippy_aitb_inline_color')) {
    /**
     * Build a single inline CSS rule, or empty string when value is missing.
     */
    function ai_zippy_aitb_inline_color($prop, $value)
    {
        if (empty($value)) {
            return '';
        }
        return $prop . ': ' . esc_attr($value) . ';';
    }
}

$items                 = isset($attributes['items']) && is_array($attributes['items']) ? $attributes['items'] : [];
$columns               = isset($attributes['columns']) ? max(1, min(6, (int) $attributes['columns'])) : 3;
$bg_color              = $attributes['bgColor'] ?? '#ffffff';
$bg_image_url          = $attributes['bgImageUrl'] ?? '';
$bg_overlay_color      = $attributes['bgOverlayColor'] ?? 'rgba(255,250,243,0.85)';
$section_heading       = $attributes['sectionHeading'] ?? '';
$section_heading_color = $attributes['sectionHeadingColor'] ?? '#3B2715';

$style_parts = [
    'background-color: ' . esc_attr($bg_color),
    '--aitb-columns: ' . (int) $columns,
    '--aitb-overlay: ' . esc_attr($bg_overlay_color),
];

if (!empty($bg_image_url)) {
    $style_parts[] = 'background-image: url(' . esc_url($bg_image_url) . ')';
    $style_parts[] = 'background-size: cover';
    $style_parts[] = 'background-position: center';
}

$wrapper_classes = 'aitb' . (!empty($bg_image_url) ? ' aitb--has-bg' : '');

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => $wrapper_classes,
    'style' => implode('; ', $style_parts) . ';',
]);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if (!empty($bg_image_url)) : ?>
        <span class="aitb__bg-overlay" aria-hidden="true"></span>
    <?php endif; ?>
    <div class="aitb__inner">
        <?php if (!empty($section_heading)) : ?>
            <h2
                class="aitb__section-heading"
                style="<?php echo esc_attr(ai_zippy_aitb_inline_color('color', $section_heading_color)); ?>"
            >
                <?php echo wp_kses_post($section_heading); ?>
            </h2>
        <?php endif; ?>

        <?php if (!empty($items)) : ?>
            <div class="aitb__grid">
                <?php foreach ($items as $item) :
                    $icon_url       = $item['iconUrl'] ?? '';
                    $heading        = $item['heading'] ?? '';
                    $text           = $item['text'] ?? '';
                    $icon_bg_color  = $item['iconBgColor'] ?? '#FFFAF3';
                    $heading_color  = $item['headingColor'] ?? '#3B2715';
                    $text_color     = $item['textColor'] ?? '#615245';
                ?>
                    <article class="aitb__item">
                        <div
                            class="aitb__icon-wrap<?php echo empty($icon_url) ? ' aitb__icon-wrap--empty' : ''; ?>"
                            style="<?php echo esc_attr(ai_zippy_aitb_inline_color('background-color', $icon_bg_color)); ?>"
                        >
                            <span class="aitb__icon-ring" aria-hidden="true"></span>
                            <?php if (!empty($icon_url)) : ?>
                                <img
                                    class="aitb__icon"
                                    src="<?php echo esc_url($icon_url); ?>"
                                    alt=""
                                    loading="lazy"
                                />
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($heading)) : ?>
                            <h3
                                class="aitb__heading"
                                style="<?php echo esc_attr(ai_zippy_aitb_inline_color('color', $heading_color)); ?>"
                            >
                                <?php echo wp_kses_post($heading); ?>
                            </h3>
                        <?php endif; ?>

                        <span
                            class="aitb__divider"
                            aria-hidden="true"
                            style="<?php echo esc_attr(ai_zippy_aitb_inline_color('background-color', $heading_color)); ?>"
                        ></span>

                        <?php if (!empty($text)) : ?>
                            <p
                                class="aitb__text"
                                style="<?php echo esc_attr(ai_zippy_aitb_inline_color('color', $text_color)); ?>"
                            >
                                <?php echo wp_kses_post($text); ?>
                            </p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
