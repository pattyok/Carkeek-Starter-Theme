<?php
/**
 * Template part for displaying the header search
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>
<div class="nav--toggle-small header-search-wrapper">
	<button class="search-toggle header-toggle" data-toggleoff="primary-menu-toggle" id="search-toggle" data-target="#header-search" aria-label="<?php esc_attr_e( 'Open search', 'wp-rig' ); ?>" aria-controls="header-search" aria-expanded="false">
	<span class="search-toggle-box">
	<span class="search-toggle__circle"></span>
		<span class="search-toggle__rectangle"></span>
	</span>
	</button>
	<div class="header-search" id="header-search">
		<?php get_search_form( true ); ?>
	</div>
</div>
