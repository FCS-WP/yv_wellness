<?php

/**
 * AI Zippy Child Theme Functions
 *
 * Project-specific customizations live here.
 * The parent theme (ai-zippy) handles core assets, REST APIs, cart/checkout, etc.
 * This file adds the child's own Vite-built assets and auto-registers any
 * client-specific Gutenberg blocks from assets/blocks/.
 */

defined('ABSPATH') || exit;

// =============================================================================
// Vite manifest reader — child's own assets/dist/.vite/manifest.json
// Mirrors the parent's AiZippy\Core\ViteAssets but scoped to this theme.
// =============================================================================

/**
 * Read the child theme's Vite manifest (cached per-request).
 */
function ai_zippy_child_vite_manifest(): array
{
    static $manifest = null;
    if ($manifest !== null) {
        return $manifest;
    }

    $path = get_stylesheet_directory() . '/assets/dist/.vite/manifest.json';
    if (!file_exists($path)) {
        return $manifest = [];
    }

    $decoded = json_decode(file_get_contents($path), true);
    return $manifest = is_array($decoded) ? $decoded : [];
}

/**
 * Enqueue a child-theme Vite asset by its source entry key.
 * Entry keys are the full path from repo root, e.g.:
 *   src/wp-content/themes/ai-zippy-child/src/js/child.js
 */
function ai_zippy_child_enqueue_vite(string $handle, string $entry, array $js_deps = []): void
{
    $manifest = ai_zippy_child_vite_manifest();
    if (empty($manifest[$entry])) {
        return;
    }

    $asset    = $manifest[$entry];
    $dist_uri = get_stylesheet_directory_uri() . '/assets/dist';
    $dist_dir = get_stylesheet_directory() . '/assets/dist';

    // JS entry — skip stub files emitted by Vite when an SCSS entry has no content yet
    if (!empty($asset['file']) && str_ends_with($asset['file'], '.js')) {
        $file_path = $dist_dir . '/' . $asset['file'];
        $is_scss_entry = !empty($asset['src']) && str_ends_with($asset['src'], '.scss');
        $is_tiny_stub  = file_exists($file_path) && filesize($file_path) < 100;

        if (!($is_scss_entry && $is_tiny_stub)) {
            $version = file_exists($file_path) ? filemtime($file_path) : false;
            wp_enqueue_script($handle, $dist_uri . '/' . $asset['file'], $js_deps, $version, true);
        }
    }

    // CSS-only entry (manifest file is .css directly)
    if (!empty($asset['file']) && str_ends_with($asset['file'], '.css') && empty($asset['css'])) {
        $file_path = $dist_dir . '/' . $asset['file'];
        $version   = file_exists($file_path) ? filemtime($file_path) : false;
        // Depend on the parent theme's style so child CSS loads after it.
        wp_enqueue_style($handle, $dist_uri . '/' . $asset['file'], ['ai-zippy-theme-css-0'], $version);
    }

    // CSS bundled with JS entry
    if (!empty($asset['css'])) {
        foreach ($asset['css'] as $i => $css_file) {
            $file_path = $dist_dir . '/' . $css_file;
            $version   = file_exists($file_path) ? filemtime($file_path) : false;
            wp_enqueue_style(
                $handle . '-css-' . $i,
                $dist_uri . '/' . $css_file,
                ['ai-zippy-theme-css-0'],
                $version
            );
        }
    }
}

/**
 * Mark child theme scripts as ES modules (Vite outputs ESM).
 */
add_filter('script_loader_tag', function (string $tag, string $handle): string {
    if (str_starts_with($handle, 'ai-zippy-child')) {
        return str_replace(' src=', ' type="module" src=', $tag);
    }
    return $tag;
}, 10, 2);

/**
 * Enqueue child theme assets (after parent so overrides work).
 */
add_action('wp_enqueue_scripts', function (): void {
    $base = 'src/wp-content/themes/ai-zippy-child/src';

    ai_zippy_child_enqueue_vite('ai-zippy-child',       $base . '/js/child.js');
    ai_zippy_child_enqueue_vite('ai-zippy-child-style', $base . '/scss/style.scss');
}, 20);

// =============================================================================
// Google Fonts — Marcellus & Raleway
// =============================================================================

add_action('wp_enqueue_scripts', function (): void {
    wp_enqueue_style(
        'ai-zippy-child-google-fonts',
        'https://fonts.googleapis.com/css2?family=Marcellus&family=Raleway:wght@300;400;500;600;700&display=swap',
        [],
        null
    );
}, 5);

/**
 * Also load Google Fonts in the block editor.
 */
add_action('enqueue_block_editor_assets', function (): void {
    wp_enqueue_style(
        'ai-zippy-child-google-fonts-editor',
        'https://fonts.googleapis.com/css2?family=Marcellus&family=Raleway:wght@300;400;500;600;700&display=swap',
        [],
        null
    );
});

// =============================================================================
// Auto-register child theme blocks (wp-scripts build output)
// =============================================================================

add_action('init', function (): void {
    $blocks_dir = get_stylesheet_directory() . '/assets/blocks';
    if (!is_dir($blocks_dir)) {
        return;
    }
    foreach (glob($blocks_dir . '/*/block.json') as $block_json) {
        register_block_type(dirname($block_json));
    }
});

// =============================================================================
// Contact Form REST API endpoint.
// =============================================================================

add_action('rest_api_init', function () {
    register_rest_route('ai-zippy/v1', '/contact-submit', [
        'methods'             => 'POST',
        'callback'            => 'ai_zippy_handle_contact_form',
        'permission_callback' => '__return_true',
    ]);
});

if (!function_exists('ai_zippy_handle_contact_form')) {
    function ai_zippy_handle_contact_form(WP_REST_Request $request) {
        try {
            $params = $request->get_json_params();

            $name      = sanitize_text_field($params['name'] ?? '');
            $email     = sanitize_email($params['email'] ?? '');
            $phone     = sanitize_text_field($params['phone'] ?? '');
            $subject   = sanitize_text_field($params['subject'] ?? 'New Contact Form Submission');
            $message   = sanitize_textarea_field($params['message'] ?? '');
            $recipient = sanitize_email($params['recipient'] ?? 'yvwellnesssgp@gmail.com');

            // Validate required fields
            if (empty($name) || empty($email) || empty($message)) {
                return new WP_REST_Response([
                    'status'  => 'error',
                    'message' => 'Please fill in all required fields (name, email, message).',
                ], 400);
            }

            if (!is_email($email)) {
                return new WP_REST_Response([
                    'status'  => 'error',
                    'message' => 'Please provide a valid email address.',
                ], 400);
            }

            // Store submission in database (never lose a message)
            $submission = [
                'name'      => $name,
                'email'     => $email,
                'phone'     => $phone,
                'subject'   => $subject,
                'message'   => $message,
                'recipient' => $recipient,
                'date'      => current_time('mysql'),
                'mail_sent' => false,
            ];

            $submissions = get_option('ai_zippy_contact_submissions', []);
            $submissions[] = $submission;
            // Keep last 100 submissions
            if (count($submissions) > 100) {
                $submissions = array_slice($submissions, -100);
            }
            update_option('ai_zippy_contact_submissions', $submissions);

            // Attempt to send email
            $email_subject = '[YV Wellness Contact] ' . ($subject ?: 'New Inquiry');
            $email_body    = "New contact form submission:\n\n";
            $email_body   .= "Name: {$name}\n";
            $email_body   .= "Email: {$email}\n";
            if ($phone) {
                $email_body .= "Phone: {$phone}\n";
            }
            $email_body .= "Subject: {$subject}\n\n";
            $email_body .= "Message:\n{$message}\n";

            $headers = [
                'Content-Type: text/plain; charset=UTF-8',
                "Reply-To: {$name} <{$email}>",
            ];

            // Capture mail errors
            $mail_error = '';
            add_action('wp_mail_failed', function($wp_error) use (&$mail_error) {
                $mail_error = $wp_error->get_error_message();
            });

            $sent = wp_mail($recipient, $email_subject, $email_body, $headers);

            if (!$sent) {
                error_log('Contact form: wp_mail() failed. Error: ' . $mail_error . ' | From: ' . $email);
            }

            // Always return success since we stored the submission
            return new WP_REST_Response([
                'status'  => 'success',
                'message' => 'Thank you! Your message has been received successfully.',
            ], 200);

        } catch (\Exception $e) {
            error_log('Contact form exception: ' . $e->getMessage());
            return new WP_REST_Response([
                'status'  => 'error',
                'message' => 'An error occurred while processing your request. Please try again.',
            ], 500);
        } catch (\Error $e) {
            error_log('Contact form fatal error: ' . $e->getMessage());
            return new WP_REST_Response([
                'status'  => 'error',
                'message' => 'An error occurred while processing your request. Please try again.',
            ], 500);
        }
    }
}

// =============================================================================
// Related posts helper for single post template.
// =============================================================================

/**
 * Get related posts from the same category.
 *
 * @param int $post_id Current post ID.
 * @param int $count   Number of related posts to return.
 * @return WP_Post[] Array of related post objects.
 */
function ai_zippy_get_related_posts(int $post_id, int $count = 4): array {
    $categories = wp_get_post_categories($post_id);

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => $count,
        'post__not_in'   => [$post_id],
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
    ];

    // Filter by category if available
    if (!empty($categories)) {
        $args['category__in'] = $categories;
    }

    $query = new WP_Query($args);
    $posts = $query->posts;
    wp_reset_postdata();

    // Fallback: if category filter returned nothing, get any recent posts
    if (empty($posts) && !empty($categories)) {
        unset($args['category__in']);
        $query = new WP_Query($args);
        $posts = $query->posts;
        wp_reset_postdata();
    }

    return $posts;
}
