<?php
/**
 * OEM Information Block — Server-side render.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks content.
 * @var WP_Block $block      Block instance.
 */

$heading           = $attributes['heading'] ?? '';
$heading_color     = $attributes['headingColor'] ?? '#3B2715';
$body              = $attributes['body'] ?? '';
$body_color        = $attributes['bodyColor'] ?? '#615245';
$button_text       = $attributes['buttonText'] ?? '';
$button_url        = $attributes['buttonUrl'] ?? 'tel:96384686';
$button_text_color = $attributes['buttonTextColor'] ?? '#FFFAF3';
$button_bg_color   = $attributes['buttonBgColor'] ?? '#3B2715';
$bg_color          = $attributes['backgroundColor'] ?? '#FFFAF3';

$wrapper_attrs = get_block_wrapper_attributes([
    'class'        => 'oem-info',
    'data-animate' => 'fade-up',
    'style'        => 'background-color:' . esc_attr($bg_color),
]);
?>
<section <?php echo $wrapper_attrs; ?>>
    <div class="oem-info__container stagger-group" data-animate="fade-up">
        <span class="oem-info__ornament" aria-hidden="true" data-animate-child></span>

        <?php if ($heading) : ?>
            <h2 class="oem-info__heading"
                style="color:<?php echo esc_attr($heading_color); ?>"
                data-animate-child>
                <?php echo wp_kses_post($heading); ?>
            </h2>
        <?php endif; ?>

        <?php if ($body) : ?>
            <p class="oem-info__body"
               style="color:<?php echo esc_attr($body_color); ?>"
               data-animate-child>
                <?php echo wp_kses_post($body); ?>
            </p>
        <?php endif; ?>

        <?php if ($button_text) : ?>
            <div class="oem-info__btn-wrap" data-animate-child>
                <a class="oem-info__btn"
                   href="<?php echo esc_url($button_url); ?>"
                   style="color:<?php echo esc_attr($button_text_color); ?>; background-color:<?php echo esc_attr($button_bg_color); ?>">
                    <?php echo wp_kses_post($button_text); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
