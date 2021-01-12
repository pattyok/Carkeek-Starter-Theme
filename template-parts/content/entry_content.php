<?php
/**
 * Template part for displaying a post's content
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$show_sharing = get_theme_mod( 'wprig_show_post_sharing' );
?>

<div class="entry-content page-content">
<div class="entry-header">
	<?php get_template_part( 'template-parts/content/entry_meta', get_post_type() ); ?>
	<?php
	if ( true == $show_sharing ) {
		wp_rig()->make_social_share_links();
	}
	?>
</div>
	<?php
	the_content(
		sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'wp-rig' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		)
	);

	wp_link_pages(
		array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'wp-rig' ),
			'after'  => '</div>',
		)
	);
	?>
</div><!-- .entry-content -->
