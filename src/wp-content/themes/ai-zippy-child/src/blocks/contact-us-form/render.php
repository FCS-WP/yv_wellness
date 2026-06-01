<?php
/**
 * Contact Us Form Block — Server-side render.
 */

if (!function_exists('cuf_render_phone_icon')) {
    function cuf_render_phone_icon() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
    }
}

if (!function_exists('cuf_render_email_icon')) {
    function cuf_render_email_icon() {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>';
    }
}

$phone           = $attributes['phone'] ?? '(+65) 96384686 / 97128668';
$email_addr      = $attributes['email'] ?? 'yvwellnesssgp@gmail.com';
$form_title      = $attributes['formTitle'] ?? 'Send us a message';
$submit_text     = $attributes['submitText'] ?? 'Send Message';
$recipient_email = $attributes['recipientEmail'] ?? 'yvwellnesssgp@gmail.com';

$wrapper_attrs = get_block_wrapper_attributes([
    'class'        => 'cuf',
    'data-animate' => 'fade-up',
]);

$nonce = wp_create_nonce('wp_rest');
$api_url = esc_url(rest_url('ai-zippy/v1/contact-submit'));
?>
<section <?php echo $wrapper_attrs; ?>>
    <div class="cuf__grid">
        <!-- Info Cards -->
        <div class="cuf__info" data-animate="slide-left">
            <div class="cuf__card hover-lift-sm">
                <span class="cuf__card-icon"><?php echo cuf_render_phone_icon(); ?></span>
                <h4 class="cuf__card-title">Phone:</h4>
                <p class="cuf__card-text">
                    <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', explode('/', $phone)[0])); ?>">
                        <?php echo esc_html($phone); ?>
                    </a>
                </p>
            </div>
            <div class="cuf__card hover-lift-sm">
                <span class="cuf__card-icon"><?php echo cuf_render_email_icon(); ?></span>
                <h4 class="cuf__card-title">Email:</h4>
                <p class="cuf__card-text">
                    <a href="mailto:<?php echo esc_attr($email_addr); ?>">
                        <?php echo esc_html($email_addr); ?>
                    </a>
                </p>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="cuf__form-wrap" data-animate="slide-right">
            <h3 class="cuf__form-title"><?php echo esc_html($form_title); ?></h3>
            <form class="cuf__form" id="cuf-contact-form" novalidate>
                <input type="hidden" name="_wpnonce" value="<?php echo esc_attr($nonce); ?>" />
                <input type="hidden" name="recipient" value="<?php echo esc_attr($recipient_email); ?>" />

                <div class="cuf__field">
                    <label for="cuf-name">Name *</label>
                    <input type="text" id="cuf-name" name="name" required placeholder="Your full name" />
                </div>

                <div class="cuf__field-row">
                    <div class="cuf__field">
                        <label for="cuf-email">Email *</label>
                        <input type="email" id="cuf-email" name="email" required placeholder="your@email.com" />
                    </div>
                    <div class="cuf__field">
                        <label for="cuf-phone">Phone</label>
                        <input type="tel" id="cuf-phone" name="phone" placeholder="+65 1234 5678" />
                    </div>
                </div>

                <div class="cuf__field">
                    <label for="cuf-subject">Subject</label>
                    <input type="text" id="cuf-subject" name="subject" placeholder="What is this about?" />
                </div>

                <div class="cuf__field">
                    <label for="cuf-message">Message *</label>
                    <textarea id="cuf-message" name="message" rows="5" required placeholder="Your message..."></textarea>
                </div>

                <button type="submit" class="cuf__submit">
                    <span class="cuf__submit-text"><?php echo esc_html($submit_text); ?></span>
                    <span class="cuf__submit-loading" style="display:none;">Sending...</span>
                </button>

                <div class="cuf__feedback" style="display:none;" role="alert"></div>
            </form>
        </div>
    </div>
</section>

<script>
(function() {
    const form = document.getElementById('cuf-contact-form');
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = form.querySelector('.cuf__submit');
        const submitText = form.querySelector('.cuf__submit-text');
        const submitLoading = form.querySelector('.cuf__submit-loading');
        const feedback = form.querySelector('.cuf__feedback');

        // Reset feedback
        feedback.style.display = 'none';
        feedback.className = 'cuf__feedback';

        // Validate
        const name = form.querySelector('[name="name"]').value.trim();
        const email = form.querySelector('[name="email"]').value.trim();
        const message = form.querySelector('[name="message"]').value.trim();

        if (!name || !email || !message) {
            feedback.textContent = 'Please fill in all required fields.';
            feedback.classList.add('cuf__feedback--error');
            feedback.style.display = 'block';
            return;
        }

        // Show loading
        submitBtn.disabled = true;
        submitText.style.display = 'none';
        submitLoading.style.display = 'inline';

        try {
            const response = await fetch('<?php echo $api_url; ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': form.querySelector('[name="_wpnonce"]').value,
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    phone: form.querySelector('[name="phone"]').value.trim(),
                    subject: form.querySelector('[name="subject"]').value.trim(),
                    message: message,
                    recipient: form.querySelector('[name="recipient"]').value,
                }),
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                feedback.textContent = data.message || 'Message sent successfully!';
                feedback.classList.add('cuf__feedback--success');
                form.reset();
            } else {
                feedback.textContent = data.message || 'Failed to send message. Please try again.';
                feedback.classList.add('cuf__feedback--error');
            }
        } catch (err) {
            feedback.textContent = 'Network error. Please try again later.';
            feedback.classList.add('cuf__feedback--error');
        }

        feedback.style.display = 'block';
        submitBtn.disabled = false;
        submitText.style.display = 'inline';
        submitLoading.style.display = 'none';
    });
})();
</script>
