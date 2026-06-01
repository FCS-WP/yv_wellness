<?php
/**
 * Events Gallery Block — Server-side render.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks content.
 * @var WP_Block $block      Block instance.
 */

$items          = $attributes['items'] ?? [];
$columns        = $attributes['columns'] ?? 3;
$gap            = $attributes['gap'] ?? 10;
$overlay_color  = $attributes['overlayColor'] ?? 'rgba(0, 0, 0, 0.5)';
$overlay_text   = $attributes['overlayTextColor'] ?? '#ffffff';
$border_radius  = $attributes['borderRadius'] ?? 12;

if (empty($items)) {
    return;
}

// Unique ID so multiple instances on the same page each control their own lightbox.
$instance_id = wp_unique_id('eg-');

$wrapper_attrs = get_block_wrapper_attributes([
    'class' => 'eg',
    'id'    => $instance_id,
]);

$grid_style = sprintf(
    '--eg-columns:%d;--eg-gap:%dpx;--eg-radius:%dpx;--eg-overlay:%s;--eg-overlay-text:%s',
    $columns,
    $gap,
    $border_radius,
    esc_attr($overlay_color),
    esc_attr($overlay_text)
);
?>
<section <?php echo $wrapper_attrs; ?>>
    <div class="eg__grid" style="<?php echo esc_attr($grid_style); ?>">
        <?php foreach ($items as $index => $item) :
            $url     = $item['url'] ?? '';
            $alt     = $item['alt'] ?? '';
            $caption = $item['caption'] ?? '';
            $span    = $item['span'] ?? 'normal';
            $class   = 'eg__item' . ($span === 'large' ? ' eg__item--large' : '');

            if (empty($url)) continue;
        ?>
            <article
                class="<?php echo esc_attr($class); ?>"
                tabindex="0"
                role="button"
                aria-label="<?php echo esc_attr($alt ?: __('Open image', 'ai-zippy-child')); ?>"
            >
                <div class="eg__image-wrap">
                    <img
                        class="eg__image"
                        src="<?php echo esc_url($url); ?>"
                        alt="<?php echo esc_attr($alt); ?>"
                        loading="lazy"
                    />
                    <?php if ($caption) : ?>
                        <div class="eg__overlay">
                            <span class="eg__caption"><?php echo wp_kses_post($caption); ?></span>
                        </div>
                    <?php else : ?>
                        <div class="eg__overlay"></div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="eg__lightbox" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Image preview', 'ai-zippy-child'); ?>" hidden>
        <div class="eg__lightbox-overlay" aria-hidden="true"></div>
        <button type="button" class="eg__lightbox-close" aria-label="<?php esc_attr_e('Close', 'ai-zippy-child'); ?>">&times;</button>
        <img class="eg__lightbox-img" src="" alt="" />
        <p class="eg__lightbox-caption"></p>
    </div>
</section>
<script>
(function () {
    var section = document.getElementById('<?php echo esc_js($instance_id); ?>');
    if (!section || section.dataset.egInit === '1') return;
    section.dataset.egInit = '1';

    var lightbox = section.querySelector('.eg__lightbox');
    if (!lightbox) return;

    var lightboxImg     = lightbox.querySelector('.eg__lightbox-img');
    var lightboxCaption = lightbox.querySelector('.eg__lightbox-caption');
    var overlay         = lightbox.querySelector('.eg__lightbox-overlay');
    var closeBtn        = lightbox.querySelector('.eg__lightbox-close');
    var lastFocus       = null;

    function openLightbox(src, alt, captionHtml) {
        lastFocus = document.activeElement;
        lightboxImg.src = src;
        lightboxImg.alt = alt || '';
        lightboxCaption.innerHTML = captionHtml || '';
        lightbox.removeAttribute('hidden');
        // Force reflow so the transition runs.
        void lightbox.offsetWidth;
        lightbox.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        closeBtn.focus();
    }

    function closeLightbox() {
        if (!lightbox.classList.contains('is-open')) return;
        lightbox.classList.remove('is-open');
        document.body.style.overflow = '';
        var onEnd = function () {
            lightbox.setAttribute('hidden', '');
            lightboxImg.src = '';
            lightbox.removeEventListener('transitionend', onEnd);
        };
        lightbox.addEventListener('transitionend', onEnd);
        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }
    }

    section.querySelectorAll('.eg__item').forEach(function (item) {
        var img     = item.querySelector('.eg__image');
        var caption = item.querySelector('.eg__caption');
        if (!img) return;

        var trigger = function (e) {
            e.preventDefault();
            openLightbox(img.src, img.alt, caption ? caption.innerHTML : '');
        };

        item.addEventListener('click', trigger);
        item.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                trigger(e);
            }
        });
    });

    overlay.addEventListener('click', closeLightbox);
    closeBtn.addEventListener('click', closeLightbox);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && lightbox.classList.contains('is-open')) {
            closeLightbox();
        }
    });
})();
</script>
