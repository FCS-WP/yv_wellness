<?php
$title = esc_html($attributes['title'] ?? "Our Customer's Journey");
$title_color = esc_attr($attributes['titleColor'] ?? '#7c4612');
$images = $attributes['images'] ?? [];
$image_count = count($images);

$wrapper_attributes = get_block_wrapper_attributes([
  'class' => 'hg',
  'data-animate' => 'fade-up',
]);
?>
<section <?php echo $wrapper_attributes; ?>>
  <?php if ($title) : ?>
    <h2 class="hg__title" style="color: <?php echo $title_color; ?>"><?php echo $title; ?></h2>
  <?php endif; ?>

  <?php if (!empty($images)) : ?>
    <div class="hg__carousel-wrap">
      <button type="button" class="hg__nav-btn hg__nav-btn--prev" aria-label="<?php esc_attr_e('Previous', 'ai-zippy-child'); ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
      </button>

      <div class="hg__carousel" role="region" aria-label="<?php esc_attr_e('Gallery carousel', 'ai-zippy-child'); ?>" data-original-count="<?php echo (int) $image_count; ?>">
        <?php foreach ($images as $index => $image) : ?>
          <div class="hg__slide" data-index="<?php echo (int) $index; ?>">
            <img
              class="hg__image"
              src="<?php echo esc_url($image['url'] ?? ''); ?>"
              alt="<?php echo esc_attr($image['alt'] ?? ''); ?>"
              loading="lazy"
            />
          </div>
        <?php endforeach; ?>
        <?php // Duplicate set for seamless infinite loop ?>
        <?php foreach ($images as $index => $image) : ?>
          <div class="hg__slide hg__slide--clone" data-index="<?php echo (int) $index; ?>" aria-hidden="true">
            <img
              class="hg__image"
              src="<?php echo esc_url($image['url'] ?? ''); ?>"
              alt=""
              loading="lazy"
              aria-hidden="true"
            />
          </div>
        <?php endforeach; ?>
      </div>

      <button type="button" class="hg__nav-btn hg__nav-btn--next" aria-label="<?php esc_attr_e('Next', 'ai-zippy-child'); ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
      </button>

      <ul class="hg__dots" role="tablist" aria-label="<?php esc_attr_e('Gallery slides', 'ai-zippy-child'); ?>">
        <?php foreach ($images as $index => $image) : ?>
          <li>
            <button
              type="button"
              class="hg__dot<?php echo $index === 0 ? ' is-active' : ''; ?>"
              data-index="<?php echo (int) $index; ?>"
              aria-label="<?php echo esc_attr(sprintf(__('Go to slide %d', 'ai-zippy-child'), $index + 1)); ?>"
            ></button>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <script>
    (function(){
      var wrap = document.currentScript.closest('.hg');
      if (!wrap) return;
      var carousel = wrap.querySelector('.hg__carousel');
      var prevBtn  = wrap.querySelector('.hg__nav-btn--prev');
      var nextBtn  = wrap.querySelector('.hg__nav-btn--next');
      var dots     = wrap.querySelectorAll('.hg__dot');
      var slides   = wrap.querySelectorAll('.hg__slide');
      if (!carousel || !slides.length) return;

      var originalCount = parseInt(carousel.getAttribute('data-original-count'), 10) || Math.floor(slides.length / 2) || slides.length;

      function getStep() {
        var first = slides[0];
        if (!first) return carousel.clientWidth;
        var styles = window.getComputedStyle(carousel);
        var gap = parseFloat(styles.columnGap || styles.gap || '0') || 0;
        return first.offsetWidth + gap;
      }

      function originalWidth() {
        return getStep() * originalCount;
      }

      function setActiveDot(index) {
        if (!dots.length) return;
        dots.forEach(function(dot, i){
          if (i === index) dot.classList.add('is-active');
          else dot.classList.remove('is-active');
        });
      }

      function currentIndex() {
        var step = getStep();
        if (step <= 0 || originalCount <= 0) return 0;
        var idx = Math.round(carousel.scrollLeft / step) % originalCount;
        if (idx < 0) idx += originalCount;
        return idx;
      }

      function instantJump(target) {
        var prev = carousel.style.scrollBehavior;
        carousel.style.scrollBehavior = 'auto';
        carousel.scrollLeft = target;
        // Force reflow so the next smooth scroll starts from the new position
        void carousel.offsetWidth;
        carousel.style.scrollBehavior = prev || '';
      }

      function normalizePosition() {
        var w = originalWidth();
        if (w <= 0) return;
        if (carousel.scrollLeft >= w) {
          instantJump(carousel.scrollLeft - w);
        } else if (carousel.scrollLeft <= 0) {
          instantJump(carousel.scrollLeft + w);
        }
      }

      if (nextBtn) {
        nextBtn.addEventListener('click', function() {
          var step = getStep();
          carousel.scrollBy({ left: step, behavior: 'smooth' });
        });
      }

      if (prevBtn) {
        prevBtn.addEventListener('click', function() {
          var step = getStep();
          // Pre-jump to equivalent position in the duplicated set so we can
          // smoothly scroll backwards across the boundary without flicker.
          if (carousel.scrollLeft <= 1) {
            instantJump(originalWidth());
          }
          carousel.scrollBy({ left: -step, behavior: 'smooth' });
        });
      }

      dots.forEach(function(dot){
        dot.addEventListener('click', function(){
          var index = parseInt(dot.getAttribute('data-index'), 10) || 0;
          var step = getStep();
          // Always navigate within the original set range so dots map 1:1.
          if (carousel.scrollLeft >= originalWidth()) {
            instantJump(carousel.scrollLeft - originalWidth());
          }
          carousel.scrollTo({ left: step * index, behavior: 'smooth' });
        });
      });

      var rafId = null;
      var scrollEndTimer = null;
      carousel.addEventListener('scroll', function(){
        if (!rafId) {
          rafId = window.requestAnimationFrame(function(){
            rafId = null;
            setActiveDot(currentIndex());
          });
        }
        if (scrollEndTimer) clearTimeout(scrollEndTimer);
        scrollEndTimer = setTimeout(normalizePosition, 140);
      });

      // Drag-to-scroll (pointer-based, works for mouse and touch)
      var isDown = false;
      var startX = 0;
      var startScroll = 0;
      var dragMoved = false;
      var dragJumped = false;
      var DRAG_THRESHOLD = 5;

      carousel.addEventListener('pointerdown', function(e){
        isDown = true;
        dragMoved = false;
        dragJumped = false;
        startX = e.pageX - carousel.offsetLeft;
        startScroll = carousel.scrollLeft;
        try { carousel.setPointerCapture(e.pointerId); } catch(_){}
      });

      carousel.addEventListener('pointermove', function(e){
        if (!isDown) return;
        var x = e.pageX - carousel.offsetLeft;
        var walk = (x - startX) * 1.5;
        if (Math.abs(walk) > DRAG_THRESHOLD && !dragMoved) {
          dragMoved = true;
          carousel.classList.add('is-dragging');
        }
        if (dragMoved) {
          e.preventDefault();
          // If user drags backward (right) from the start, jump into the
          // duplicate set first so the drag can continue past the boundary.
          if (!dragJumped && walk > 0 && startScroll <= 1) {
            dragJumped = true;
            var w = originalWidth();
            startScroll = w;
            carousel.scrollLeft = w;
          }
          carousel.scrollLeft = startScroll - walk;
        }
      });

      function endDrag(e){
        if (!isDown) return;
        isDown = false;
        carousel.classList.remove('is-dragging');
        try { carousel.releasePointerCapture(e.pointerId); } catch(_){}
        // Suppress click-through after a drag (prevents accidental link/img activation)
        if (dragMoved) {
          var swallow = function(ev){
            ev.stopPropagation();
            ev.preventDefault();
            carousel.removeEventListener('click', swallow, true);
          };
          carousel.addEventListener('click', swallow, true);
          // Normalize after the drag settles
          if (scrollEndTimer) clearTimeout(scrollEndTimer);
          scrollEndTimer = setTimeout(normalizePosition, 140);
        }
      }

      carousel.addEventListener('pointerup', endDrag);
      carousel.addEventListener('pointercancel', endDrag);
      carousel.addEventListener('pointerleave', endDrag);

      // Prevent native image drag from interfering
      slides.forEach(function(slide){
        var img = slide.querySelector('img');
        if (img) img.addEventListener('dragstart', function(ev){ ev.preventDefault(); });
      });
    })();
    </script>
  <?php else : ?>
    <p class="hg__empty">No images selected. Add images in the block settings.</p>
  <?php endif; ?>
</section>
