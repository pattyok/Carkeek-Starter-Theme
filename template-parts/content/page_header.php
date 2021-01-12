<?php
/**
 * Template part for displaying the page header of the currently displayed page
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

if ( is_404() ) {
	?>
	<header class="page-header">
		<h1 class="page-title">
			<?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'wp-rig' ); ?>
		</h1>
	</header><!-- .page-header -->
	<?php
} elseif ( is_home() && ! have_posts() ) {
	?>
	<header class="page-header">
		<h1 class="page-title">
			<?php esc_html_e( 'Nothing Found', 'wp-rig' ); ?>
		</h1>
	</header><!-- .page-header -->
	<?php
} elseif ( is_home() && ! is_front_page() ) {
	?>
	<header class="page-header">
		<h1 class="page-title">
			<?php single_post_title(); ?>
		</h1>
	</header><!-- .page-header -->
	<?php
} elseif ( is_search() ) {
	?>
	<header class="page-header">
		<h1 class="page-title">
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Search Results for: %s', 'wp-rig' ),
				'<span>' . get_search_query() . '</span>'
			);
			?>
		</h1>
	</header><!-- .page-header -->
	<?php
} elseif ( is_archive() ) {
	$image        = '';
	$header_class = '';
	if ( is_category() ) {
		$term = get_queried_object();
		$image  = get_field( 'category_image', $term );
	}
	if ( ! empty( $image ) ) {
		$header_class .= 'has-post-thumbnail';
	}
	?>
	<header class="page-header <?php echo esc_attr( $header_class ); ?>">
		<?php

		if ( ! empty( $image ) ) {
			?>
			<div class="post-thumbnail">
			<?php echo wp_get_attachment_image( $image, 'large' ); ?>

		</div><!-- .post-thumbnail -->
		<div class="header-content">
			<?php
		}
		the_archive_title( '<h1 class="page-title">', '</h1>' );
		the_archive_description( '<div class="archive-description">', '</div>' );
		?>
		</div>
	</header><!-- .page-header -->
	<?php
} elseif ( ! is_front_page() ) {
	$hide_title     = filter_var( get_post_meta( $post->ID, '_carkeekblocks_title_hidden', true ), FILTER_VALIDATE_BOOLEAN );
	$hide_image     = filter_var( get_post_meta( $post->ID, '_carkeekblocks_featuredimage_hidden', true ), FILTER_VALIDATE_BOOLEAN );
	$header_class   = '';
	$header_content = '';
	if ( true !== $hide_image && has_post_thumbnail() ) {
		$header_class .= 'has-post-thumbnail';
	}

	?>
	<header class="page-header <?php echo esc_attr( $header_class ); ?>">
	<?php
	if ( true !== $hide_image ) {
		get_template_part( 'template-parts/content/entry_thumbnail', get_post_type() );
	}
	if ( true !== $hide_title ) {
		the_title( '<h1 class="entry-title">', '</h1>' );
	}
	?>
	</header><!-- .page-header -->
	<?php
}
