<?php
/**
 * WP_Rig\WP_Rig\Base_Support\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Shortcodes;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;

use function add_action;


/**
 * Class for adding basic theme support, most of which is mandatory to be implemented by all themes.
 *
 * Exposes template tags:
 * * `wp_rig()->get_version()`
 * * `wp_rig()->get_asset_version( string $filepath )`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'shortcodes';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {

		add_action( 'init', array( $this, 'add_custom_shortcodes' ) );

	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return array(
			'get_contactinfo'      => array( $this, 'get_contactinfo' ),
			'get_social_links'     => array( $this, 'get_social_links' ),
			'get_category_listing' => array( $this, 'get_category_listing' ),
		);
	}

	/**
	 * Set social links in options (ACF)
	 */
	public function get_social_links() {
		$links = '';
		if ( function_exists( 'get_field' ) ) {
			// Check rows exists.
			if ( have_rows( 'social_links', 'option' ) ) :
				$links .= '<ul class="social-links list-inline">';
				// Loop through rows.
				while ( have_rows( 'social_links', 'option' ) ) :
					the_row();
					$links .= '<li><a href="' . get_sub_field( 'link' ) . ' " title="' . get_sub_field( 'alt_text' ) . '"><i class="icon-' . get_sub_field( 'icon' ) . '" aria-hidden="true"></a></i>';

					// End loop.
				endwhile;

				// Do something...
			endif;
		}
		return $links;
	}

	/**
	 * Get Contact Info from Options (ACF)
	 */
	public function get_contactinfo() {
		if ( function_exists( 'get_field' ) ) {
			$contact = get_field( 'contact_email', 'option' );
			if ( ! empty( $contact ) ) {
				return '<a href="' . $contact['url'] . '">' . $contact['title'] . '</a>';
			}
		}
	}

	/**
	 * Quick and dirty way to get an item resembling the link tiles block
	 */
	public function get_category_listing() {
		$cats = get_categories();

		ob_start();
		?>
		<?php
		$count = 0;
		$total = count( $cats );
		foreach ( $cats as $cat ) {
			if ( 0 === $count || 0 === $count % 3 ) {
				?>
				<div class="wp-block-carkeek-blocks-link-tiles alignwide wp-block-columns link-tiles-shortcode">
				<?php
			}
			$count++;
			$image     = get_field( 'category_image', $cat );
			$image_url = wp_get_attachment_image_url( $image, 'large' );
			?>
			<div class="wp-block-carkeek-blocks-link-tile has-theme-green-background-color wp-block-column">
				<a class="wp-block-carkeek-blocks-link-tile__link wp-block-carkeek-blocks-link-tile__inner" href="/category/<?php echo esc_url( $cat->slug ); ?>/">
					<div style="background-image:url( <?php echo esc_url( $image_url ); ?> )" class="wp-block-carkeek-blocks-link-tile__img wp-block-carkeek-blocks-link-tile__inner">
						<span class="link-tile__title"><?php echo wp_kses_post( $cat->name ); ?></span>
					</div>
					<span class="link-tile__hover_title"><?php echo wp_kses_post( $cat->description ); ?></span>
				</a>
			</div>
			<?php
			if ( 0 === $count % 3 || $count === $total ) {
				?>
		</div>
				<?php
			}
		}
		?>

		<?php
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	/** Add Shortcodes to Theme.
	 * Mostly these are for use in widget areas.
	 */
	public function add_custom_shortcodes() {
		add_shortcode(
			'contact',
			function() {
				return $this->get_contactinfo();
			}
		);

		add_shortcode(
			'social_links',
			function() {
				return $this->get_social_links();
			}
		);

		add_shortcode(
			'category_listing',
			function() {
				return $this->get_category_listing();
			}
		);
	}


}
