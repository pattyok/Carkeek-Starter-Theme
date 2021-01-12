<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

if ( ! wp_rig()->is_secondary_nav_menu_active() ) {
	return;
}

?>

<nav id="top-navigation" class="top-navigation " aria-label="<?php esc_attr_e( 'Top menu', 'wp-rig' ); ?>">
	<div class="top-menu-container">
		<?php wp_rig()->display_secondary_nav_menu( array( 'menu_id' => 'secondary' ) ); ?>
	</div>
</nav><!-- #site-navigation -->
