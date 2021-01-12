<?php
/**
 * Template part for displaying a post's summary
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$excerpt_length = ! empty( get_theme_mod( 'wprig_excerpt_length' ) ) ? get_theme_mod( 'wprig_excerpt_length' ) : 25;
?>

<div class="entry-summary">
	<?php echo wp_kses_post( wp_rig()->get_custom_excerpt( $excerpt_length ) ); ?>
</div><!-- .entry-summary -->
