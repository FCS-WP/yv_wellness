<?php
$subtitle = esc_html($attributes['subtitle'] ?? 'OUR PROMISE');
$heading = wp_kses_post($attributes['heading'] ?? '');
$description = wp_kses_post($attributes['description'] ?? '');
$features = $attributes['features'] ?? [];
$image_url = esc_url($attributes['imageUrl'] ?? '');
$feature_title_color = esc_attr($attributes['featureTitleColor'] ?? '#3B2715');
$feature_text_color = esc_attr($attributes['featureTextColor'] ?? '#615245');

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'hp']);
?>
<section <?php echo $wrapper_attributes; ?>>
  <div class="hp__grid">
    <div class="hp__content">
      <?php if ($subtitle) : ?>
        <p class="hp__subtitle"><?php echo $subtitle; ?></p>
      <?php endif; ?>
      <?php if ($heading) : ?>
        <h2 class="hp__heading"><?php echo $heading; ?></h2>
      <?php endif; ?>
      <?php if ($description) : ?>
        <p class="hp__desc"><?php echo $description; ?></p>
      <?php endif; ?>
      <?php if (!empty($features)) : ?>
        <ul class="hp__features">
          <?php foreach ($features as $feature) : ?>
            <li class="hp__feature">
              <img class="hp__check" src="/wp-content/uploads/2026/05/brow-correct-tick.webp" alt="checkmark" width="24" height="24" loading="lazy" />
              <div class="hp__feature-text">
                <strong style="color: <?php echo $feature_title_color; ?>"><?php echo esc_html($feature['title'] ?? ''); ?></strong>
                <span style="color: <?php echo $feature_text_color; ?>"><?php echo esc_html($feature['text'] ?? ''); ?></span>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
    <div class="hp__image-wrap">
      <?php if ($image_url) : ?>
        <img class="hp__image" src="<?php echo $image_url; ?>" alt="<?php echo $subtitle; ?>" loading="lazy" />
      <?php endif; ?>
    </div>
  </div>
</section>
