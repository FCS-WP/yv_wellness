<?php
/**
 * Server-side render: ai-zippy/banner-header-page
 *
 * @package AiZippyChild
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Saved inner content (unused — server-rendered).
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ai_zippy_bhp_build_overlay_rgba' ) ) {
	/**
	 * Build an rgba() string from a base color (hex or rgba) and opacity (0-100).
	 *
	 * @param string $color   Base color.
	 * @param int    $opacity Opacity 0-100.
	 * @return string
	 */
	function ai_zippy_bhp_build_overlay_rgba( $color, $opacity ) {
		$opacity = max( 0, min( 100, (int) $opacity ) ) / 100;
		$color   = is_string( $color ) ? trim( $color ) : '';

		if ( '' === $color ) {
			return 'rgba(59,39,21,' . $opacity . ')';
		}

		// rgba(...) or rgb(...) — replace alpha.
		if ( preg_match( '/^rgba?\(([^)]+)\)$/i', $color, $m ) ) {
			$parts = array_map( 'trim', explode( ',', $m[1] ) );
			if ( count( $parts ) >= 3 ) {
				return 'rgba(' . $parts[0] . ',' . $parts[1] . ',' . $parts[2] . ',' . $opacity . ')';
			}
		}

		// Hex (#fff or #ffffff).
		if ( preg_match( '/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', $color, $m ) ) {
			$hex = $m[1];
			if ( 3 === strlen( $hex ) ) {
				$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
			}
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
			return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $opacity . ')';
		}

		return $color;
	}
}

if ( ! function_exists( 'ai_zippy_bhp_default_breadcrumb' ) ) {
	/**
	 * Build a default "Home / {Page Title}" breadcrumb fallback.
	 *
	 * @return string
	 */
	function ai_zippy_bhp_default_breadcrumb() {
		$title = '';
		if ( is_singular() ) {
			$title = get_the_title();
		} elseif ( is_archive() ) {
			$title = wp_strip_all_tags( get_the_archive_title() );
		} elseif ( is_search() ) {
			$title = __( 'Search Results', 'ai-zippy' );
		} elseif ( is_404() ) {
			$title = __( 'Not Found', 'ai-zippy' );
		}

		$home = __( 'Home', 'ai-zippy' );
		return $title ? $home . ' / ' . $title : $home;
	}
}

$heading          = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$heading_color    = isset( $attributes['headingColor'] ) ? $attributes['headingColor'] : '#ffffff';
$breadcrumb       = isset( $attributes['breadcrumb'] ) ? $attributes['breadcrumb'] : '';
$breadcrumb_color = isset( $attributes['breadcrumbColor'] ) ? $attributes['breadcrumbColor'] : 'rgba(255,255,255,0.85)';
$bg_color         = isset( $attributes['bgColor'] ) ? $attributes['bgColor'] : '#3B2715';
$bg_image_url     = isset( $attributes['bgImageUrl'] ) ? $attributes['bgImageUrl'] : '';
$overlay_color    = isset( $attributes['overlayColor'] ) ? $attributes['overlayColor'] : 'rgba(59,39,21,0.65)';
$overlay_opacity  = isset( $attributes['overlayOpacity'] ) ? (int) $attributes['overlayOpacity'] : 65;
$min_height       = isset( $attributes['minHeight'] ) ? (int) $attributes['minHeight'] : 280;

if ( '' === trim( wp_strip_all_tags( (string) $breadcrumb ) ) ) {
	$breadcrumb = ai_zippy_bhp_default_breadcrumb();
}

$overlay_rgba = ai_zippy_bhp_build_overlay_rgba( $overlay_color, $overlay_opacity );

$style_parts = array(
	'--bhp-min-height:' . $min_height . 'px',
);

if ( $bg_image_url ) {
	$style_parts[] = 'background-image:url(' . esc_url_raw( $bg_image_url ) . ')';
} else {
	$style_parts[] = 'background-color:' . $bg_color;
}

$inline_style = implode( ';', $style_parts ) . ';';

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'bhp' . ( $bg_image_url ? ' bhp--has-image' : ' bhp--solid' ),
		'style' => $inline_style,
	)
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<span class="bhp__overlay" aria-hidden="true" style="background-color: <?php echo esc_attr( $overlay_rgba ); ?>;"></span>
	<div class="bhp__content">
		<span class="bhp__ornament" aria-hidden="true"></span>
		<h1 class="bhp__heading" style="color: <?php echo esc_attr( $heading_color ); ?>;">
			<?php echo wp_kses_post( $heading ); ?>
		</h1>
		<p class="bhp__breadcrumb" style="color: <?php echo esc_attr( $breadcrumb_color ); ?>;">
			<?php echo wp_kses_post( $breadcrumb ); ?>
		</p>
	</div>
</section>
