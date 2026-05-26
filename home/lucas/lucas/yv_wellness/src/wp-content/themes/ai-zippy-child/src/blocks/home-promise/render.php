<?php
$subtitle = esc_html($attributes['subtitle'] ?? 'OUR PROMISE');
$heading = wp_kses_post($attributes['heading'] ?? '');
$description = wp_kses_post($attributes['description'] ?? '');
$features = $attributes['features'] ?? [];
$image_url = esc_url($attributes['imageUrl'] ?? '');

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
              <svg class="hp__check" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 13l4 4L19 7" stroke="#7c4612" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <div class="hp__feature-text">
                <strong><?php echo esc_html($feature['title'] ?? ''); ?></strong>
                <span><?php echo esc_html($feature['text'] ?? ''); ?></span>
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
