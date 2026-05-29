<?php
/**
 * Related Posts Block - Server-side render.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

$count   = $attributes['count'] ?? 4;
$heading = $attributes['heading'] ?? __('Related Blogs', 'ai-zippy-child');
$post_id = get_the_ID();

if (!$post_id) {
    return;
}

if (!function_exists('ai_zippy_get_related_posts')) {
    return;
}

$related = ai_zippy_get_related_posts($post_id, $count);

if (empty($related)) {
    return;
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'single-post-related',
]);
?>
<section <?php echo $wrapper_attributes; ?> data-animate="fade-up">
    <?php if (!empty($heading)) : ?>
        <h2 class="single-post-related__heading"><?php echo esc_html($heading); ?></h2>
    <?php endif; ?>

    <div class="single-post-related__carousel-wrap">
        <button type="button" class="single-post-related__nav-btn single-post-related__nav-btn--prev" aria-label="<?php esc_attr_e('Previous', 'ai-zippy-child'); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>

        <div class="single-post-related__carousel">
            <?php foreach ($related as $index => $related_post) :
                $thumb_url = get_the_post_thumbnail_url($related_post->ID, 'medium_large');
                $permalink = get_permalink($related_post->ID);
                $title     = get_the_title($related_post->ID);
                $excerpt   = wp_trim_words(get_the_excerpt($related_post->ID), 25, '&hellip;');
            ?>
            <article class="single-post-related__card" data-animate-child>
                <a href="<?php echo esc_url($permalink); ?>" class="single-post-related__card-link">
                    <div class="single-post-related__card-image">
                        <?php if ($thumb_url) : ?>
                            <img src="<?php echo esc_url($thumb_url); ?>"
                                 alt="<?php echo esc_attr($title); ?>"
                                 loading="lazy" />
                        <?php else : ?>
                            <div class="single-post-related__card-placeholder"></div>
                        <?php endif; ?>
                    </div>
                    <div class="single-post-related__card-body">
                        <h3 class="single-post-related__card-title"><?php echo esc_html($title); ?></h3>
                        <p class="single-post-related__card-excerpt"><?php echo esc_html($excerpt); ?></p>
                    </div>
                </a>
            </article>
            <?php endforeach; ?>
        </div>

        <button type="button" class="single-post-related__nav-btn single-post-related__nav-btn--next" aria-label="<?php esc_attr_e('Next', 'ai-zippy-child'); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
    </div>

    <script>
    (function(){
      var wrap = document.currentScript.closest('.single-post-related');
      if (!wrap) { wrap = document.querySelector('.single-post-related'); }
      if (!wrap) return;
      var carousel = wrap.querySelector('.single-post-related__carousel');
      var prevBtn  = wrap.querySelector('.single-post-related__nav-btn--prev');
      var nextBtn  = wrap.querySelector('.single-post-related__nav-btn--next');
      if (!carousel || !prevBtn || !nextBtn) return;

      function getScrollAmount() {
        var card = carousel.querySelector('.single-post-related__card');
        if (!card) return 300;
        return card.offsetWidth + 24; // card width + gap (1.5rem)
      }

      nextBtn.addEventListener('click', function() {
        var amount = getScrollAmount();
        if (carousel.scrollLeft + carousel.offsetWidth >= carousel.scrollWidth - 10) {
          carousel.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
          carousel.scrollBy({ left: amount, behavior: 'smooth' });
        }
      });

      prevBtn.addEventListener('click', function() {
        var amount = getScrollAmount();
        if (carousel.scrollLeft <= 10) {
          carousel.scrollTo({ left: carousel.scrollWidth, behavior: 'smooth' });
        } else {
          carousel.scrollBy({ left: -amount, behavior: 'smooth' });
        }
      });
    })();
    </script>
</section>
