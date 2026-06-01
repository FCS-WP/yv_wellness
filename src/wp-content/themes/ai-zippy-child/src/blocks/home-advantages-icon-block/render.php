<?php
$items = $attributes['items'] ?? [];
$bg_color = esc_attr($attributes['bgColor'] ?? '#615245');
$bg_image_url = esc_url($attributes['bgImageUrl'] ?? '');

$bg_style = sprintf('background-color: %s;', $bg_color);
if ($bg_image_url) {
    $bg_style .= sprintf(' background-image: url(%s); background-size: cover; background-position: center;', $bg_image_url);
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'haib',
    'style' => $bg_style,
    'data-animate' => 'fade-up',
]);
?>
<section <?php echo $wrapper_attributes; ?>>
  <div class="haib__grid">
    <?php foreach ($items as $item) : ?>
      <div class="haib__item">
        <?php if (!empty($item['iconUrl'])) : ?>
          <div class="haib__icon-wrap">
            <img class="haib__icon" src="<?php echo esc_url($item['iconUrl']); ?>" alt="<?php echo esc_attr($item['subtitle'] ?? ''); ?>" loading="lazy" />
          </div>
        <?php endif; ?>
        <?php if (!empty($item['subtitle'])) : ?>
          <p class="haib__subtitle"><?php echo wp_kses_post($item['subtitle']); ?></p>
        <?php endif; ?>
        <?php if (!empty($item['heading'])) : ?>
          <h3 class="haib__heading"><?php echo wp_kses_post($item['heading']); ?></h3>
        <?php endif; ?>
        <?php if (!empty($item['text'])) : ?>
          <p class="haib__text"><?php echo wp_kses_post($item['text']); ?></p>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>
