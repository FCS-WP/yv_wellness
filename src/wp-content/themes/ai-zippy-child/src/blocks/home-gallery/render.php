<?php
$title = esc_html($attributes['title'] ?? "Our Customer's Journey");
$title_color = esc_attr($attributes['titleColor'] ?? '#7c4612');
$images = $attributes['images'] ?? [];

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'hg']);
?>
<section <?php echo $wrapper_attributes; ?>>
  <?php if ($title) : ?>
    <h2 class="hg__title" style="color: <?php echo $title_color; ?>"><?php echo $title; ?></h2>
  <?php endif; ?>

  <?php if (!empty($images)) : ?>
    <div class="hg__track">
      <?php foreach ($images as $image) : ?>
        <div class="hg__slide">
          <img
            class="hg__image"
            src="<?php echo esc_url($image['url'] ?? ''); ?>"
            alt="<?php echo esc_attr($image['alt'] ?? ''); ?>"
            loading="lazy"
          />
        </div>
      <?php endforeach; ?>
    </div>
  <?php else : ?>
    <p class="hg__empty">No images selected. Add images in the block settings.</p>
  <?php endif; ?>
</section>
