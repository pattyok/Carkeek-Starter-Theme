<?php
/**
 * Template part for displaying the footer info
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<footer class="footer-main">
	<?php if ( is_active_sidebar( 'sidebar-footer' ) ) { ?>
		<section class="footer-section footer-upper">
			<div class="widget_wrapper">
				<?php dynamic_sidebar( 'sidebar-footer' ); ?>
			</div>
		</section>
	<?php } ?>
	<?php if ( is_active_sidebar( 'sidebar-footer-lower' ) ) { ?>
		<section class="footer-section footer-lower">
			<div class="widget_wrapper">
				<?php dynamic_sidebar( 'sidebar-footer-lower' ); ?>
			</div>
		</section>
	<?php } ?>
		<section class="footer-colophon">
			<div class="widget_wrapper">
				<?php dynamic_sidebar( 'sidebar-footer-bottom' ); ?>

			<div class="colophon">
				<ul class="colophon-info no-list">
					<li class="copyright">&copy; <?php echo esc_attr( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> All Rights Reserved</li>
					<li class="site-credits"><a class="info-popover" data-popover="site-credit-pop">Site Credits</a>
						<div class="gpopover no-list" id="site-credit-pop">
							<ul class="no-list">
								<li class="contact-info">Development: <a href="http://carkeekstudios.com"  target="_blank">Carkeek Studios</a></li>
							</ul>
						</div>
					</li>
				</ul>
			</div>
			</div>
		</section>
</footer>
