<?php
/**
 * Template part for displaying the page header of the events archive
 *
 * @package wp_rig
 */
$thumb = get_theme_mod( 'events_header_image' );
$style = ! empty( $thumb ) ? 'has-post-thumbnail' : '';

?>
<div id="ck-events-header" class="page-header <?php echo esc_attr( $style ); ?>">
	<?php
	if ( ! empty( $thumb ) ) {
		?>
		<img src="<?php echo esc_url( get_theme_mod( 'events_header_image' ) ); ?>"/>
	<?php } ?>
	<h1 id="ck-events-header-text" class="entry-title">
		<?php echo wp_kses_post( get_theme_mod( 'events_header_text' ) ); ?>
	</h1>
</div>
<?php if ( ! empty( get_theme_mod( 'events_header_intro' ) ) ) { ?>
<div id="ck-events-intro" class="ck-events-intro">
	<?php echo wp_kses_post( get_theme_mod( 'events_header_intro' ) ); ?>
</div>
<?php } ?>
