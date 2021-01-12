<?php
/**
 * WP_Rig\WP_Rig\AMP\Component class
 *
 * @package waflt_theme
 */

namespace WP_Rig\WP_Rig\Helpers;

use Tribe__Template;
use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;

/**
 * Class for template helpers
 *
 * Exposes template tags:
 * * `waflt_theme()->get_social_links()`
 *
 * @link https://wordpress.org/plugins/amp/
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'helpers';
	}

	/**
	 * Need this function even though its empty
	 */
	public function initialize() {
		add_filter( 'excerpt_more', array( $this, 'my_theme_excerpt_more' ) );
		add_filter( 'carkeek_block_custom_post_layout', array( $this, 'carkeek_block_custom_post_layout' ), 11, 3 );
		add_filter( 'carkeek_block_custom_post_layout__css_classes', array( $this, 'carkeek_block_custom_post_layout__css_classes' ), 11, 2 );
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `waflt_theme()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return array(
			'make_social_share_links' => array( $this, 'make_social_share_links' ),
			'get_custom_excerpt'      => array( $this, 'get_custom_excerpt' ),
		);
	}

	/**
	 * Make New Window Script
	 */
	private function make_new_window() {
		return "onclick=\"javascript:window.open(this.href, '_blank', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;\"";
	}

	/**
	 * Make FB Links
	 *
	 * @param string $text optional text before the icon.
	 */
	private function make_fb_button( $text = null ) {
		$url = get_the_permalink();

		$fb_link = '<a class="share-link" href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $url ) . '"' . $this->make_new_window() . ' title="Share on Facebook"><i class="icon-facebook" aria-hidden="true"></i> ' . $text . '</a>';
		return $fb_link;
	}

	/**
	 * Make Twttter Links
	 *
	 * @param string $text optional text before the icon.
	 */
	private function make_twitter_button( $text = null ) {
		$url   = get_the_permalink();
		$title = get_the_title();
		$tweet = '<a class="share-link" href="http://twitter.com/intent/tweet?text=' . $title . '&url=' . $url . '"' . $this->make_new_window() . ' title="Share on Twitter"><i class="icon-twitter" aria-hidden="true"></i>' . $text . '</a>';
		return $tweet;
	}

	/**
	 * Make Email Links
	 *
	 * @param string $text optional text before the icon.
	 */
	private function make_email_button( $text = null ) {
		$url   = get_the_permalink();
		$title = get_the_title();
		$email = '<a class="share-link" href="mailto:?subject=' . $title . '&body=' . urlencode( $url ) . '" title="Share Via Email"><i class="icon-mail"  aria-hidden="true"></i> ' . $text . '</a>';
		return $email;
	}

	/**
	 * Make Social Links
	 */
	public function make_social_share_links() {
		echo '<ul class="social-share-links list-inline">
			<li class="list-inline-item social-share-links__label">Share: </li>
			<li class="list-inline-item">' . wp_kses_post( $this->make_fb_button() ) . '</li>
			<li class="list-inline-item">' . wp_kses_post( $this->make_twitter_button() ) . '</li>
			<li class="list-inline-item">' . wp_kses_post( $this->make_email_button() ) . '</li>
		</ul>';
	}


	/**
	 * Customize the length of an excerpt
	 *
	 * @param integer $limit the number of words to return.
	 */
	public function get_custom_excerpt( $limit ) {
		$excerpt = explode( ' ', get_the_excerpt(), $limit );
		if ( count( $excerpt ) >= $limit ) {
			array_pop( $excerpt );
			$excerpt = implode( ' ', $excerpt ) . '...';
		} else {
			$excerpt = implode( ' ', $excerpt );
		}
		$excerpt = preg_replace( '`[[^]]*]`', '', $excerpt );
		return $excerpt;
	}

	/**
	 * Customize excerpt more ending
	 *
	 * @param string $more current value.
	 */
	public function my_theme_excerpt_more( $more ) {
		return '&hellip;';
	}

	/**
	 * Customize layouts for listing block
	 *
	 * @param string $post_html current value.
	 * @param array  $post current post object.
	 * @param array  $attributes block attributes.
	 */
	public function carkeek_block_custom_post_layout( $post_html, $post, $attributes ) {
		$post_type = $attributes['postTypeSelected'];
		$layout    = $post_type . '_' . $attributes['postLayout'];

		switch ( $layout ) {
			case 'post_grid':
			case 'post_list':
				ob_start();
					get_template_part( 'template-parts/content/entry', get_post_type() );
				return ob_get_clean();
			case 'tribe_events_list':
				ob_start();
				get_template_part( 'template-parts/content/entry', 'tribe_events' );
				return ob_get_clean();
			default:
				return $post_html;
		}
	}

}


