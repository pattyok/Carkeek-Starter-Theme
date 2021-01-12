<?php
/**
 * WP_Rig\WP_Rig\Base_Support\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Base_Support;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Rig\WP_Rig\Assets;
use function add_action;
use function add_filter;
use function add_theme_support;
use function is_singular;
use function pings_open;
use function esc_url;
use function get_bloginfo;
use function wp_scripts;
use function wp_get_theme;
use function get_template;

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
		return 'base_support';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', array( $this, 'action_essential_theme_support' ) );
		add_action( 'wp_head', array( $this, 'action_add_pingback_header' ) );
		add_filter( 'body_class', array( $this, 'filter_body_classes_add_hfeed' ) );
		add_filter( 'embed_defaults', array( $this, 'filter_embed_dimensions' ) );
		add_filter( 'theme_scandir_exclusions', array( $this, 'filter_scandir_exclusions_for_optional_templates' ) );
		add_filter( 'script_loader_tag', array( $this, 'filter_script_loader_tag' ), 10, 2 );
		add_filter( 'get_the_archive_title', array( $this, 'filter_archive_title' ), 10, 2 );

		add_action( 'acf/init', array( $this, 'acf_op_init' ) );

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
			'get_version'       => array( $this, 'get_version' ),
			'get_asset_version' => array( $this, 'get_asset_version' ),
			'get_asset_path'    => array( $this, 'get_asset_path' ),
		);
	}

	/**
	 * Adds theme support for essential features.
	 */
	public function action_essential_theme_support() {
		// Add default RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Ensure WordPress manages the document title.
		add_theme_support( 'title-tag' );

		// Ensure WordPress theme features render in HTML5 markup.
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		// Add support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

	}

	/**
	 * Adds a pingback url auto-discovery header for singularly identifiable articles.
	 */
	public function action_add_pingback_header() {
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
		}
	}

	/**
	 * Adds a 'hfeed' class to the array of body classes for non-singular pages.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array Filtered body classes.
	 */
	public function filter_body_classes_add_hfeed( array $classes ) : array {
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		return $classes;
	}

	/**
	 * Sets the embed width in pixels, based on the theme's design and stylesheet.
	 *
	 * @param array $dimensions An array of embed width and height values in pixels (in that order).
	 * @return array Filtered dimensions array.
	 */
	public function filter_embed_dimensions( array $dimensions ) : array {
		$dimensions['width'] = 720;
		return $dimensions;
	}

	/**
	 * Excludes any directory named 'optional' from being scanned for theme template files.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/theme_scandir_exclusions/
	 *
	 * @param array $exclusions the default directories to exclude.
	 * @return array Filtered exclusions.
	 */
	public function filter_scandir_exclusions_for_optional_templates( array $exclusions ) : array {
		return array_merge(
			$exclusions,
			array( 'optional' )
		);
	}

	/**
	 * Adds async/defer attributes to enqueued / registered scripts.
	 *
	 * If #12009 lands in WordPress, this function can no-op since it would be handled in core.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12009
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 * @return string Script HTML string.
	 */
	public function filter_script_loader_tag( string $tag, string $handle ) : string {

		foreach ( array( 'async', 'defer' ) as $attr ) {
			if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
				continue;
			}

			// Prevent adding attribute when already added in #12009.
			if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
				$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
			}

			// Only allow async or defer, not both.
			break;
		}

		return $tag;
	}

	/**
	 * Gets the theme version.
	 *
	 * @return string Theme version number.
	 */
	public function get_version() : string {
		static $theme_version = null;

		if ( null === $theme_version ) {
			$theme_version = wp_get_theme( get_template() )->get( 'Version' );
		}

		return $theme_version;
	}

	/**
	 * Gets the version for a given asset.
	 *
	 * Returns filemtime when WP_DEBUG is true, otherwise the theme version.
	 *
	 * @param string $filepath Asset file path.
	 * @return string Asset version number.
	 */
	public function get_asset_version( string $filepath ) : string {
		if ( WP_DEBUG ) {
			return (string) filemtime( $filepath );
		}

		return $this->get_version();
	}

	/**
	 * Gets the filename of the given asset.
	 *
	 * Returns filename with appended guid if the manifest exists.
	 *
	 * @param string $filename Asset file name.
	 * @return string Asset file path.
	 */
	public function get_asset_path( $filename ) {
		$file = basename( $filename );
		static $manifest;

		if ( empty( $manifest ) ) {
			$manifest_path = get_template_directory() . '/assets/rev-manifest.json';
			$manifest      = new Assets\Component( $manifest_path );
		}

		if ( array_key_exists( $file, $manifest->get() ) ) {
			return $manifest->get()[ $file ];
		} else {
			return $file;
		}
	}


	/**
	 * Filters Title Tag in archives
	 *
	 * @param string $title    The archive title.
	 * @return string Archive title stripped of excess words.
	 */
	public function filter_archive_title( $title ) {

		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif ( is_year() ) {
			$title = get_the_date( _x( 'Y', 'yearly archives date format', 'wp-rig' ) );
		} elseif ( is_month() ) {
			$title = get_the_date( _x( 'F Y', 'monthly archives date format', 'wp-rig' ) );
		} elseif ( is_day() ) {
			$title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'wp-rig' ) );
		} elseif ( is_tax( 'post_format' ) ) {
			if ( is_tax( 'post_format', 'post-format-aside' ) ) {
				$title = _x( 'Asides', 'post format archive title', 'wp-rig' );
			} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
				$title = _x( 'Galleries', 'post format archive title', 'wp-rig' );
			} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
				$title = _x( 'Images', 'post format archive title', 'wp-rig' );
			} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
				$title = _x( 'Videos', 'post format archive title', 'wp-rig' );
			} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
				$title = _x( 'Quotes', 'post format archive title', 'wp-rig' );
			} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
				$title = _x( 'Links', 'post format archive title', 'wp-rig' );
			} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
				$title = _x( 'Statuses', 'post format archive title', 'wp-rig' );
			} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
				$title = _x( 'Audio', 'post format archive title', 'wp-rig' );
			} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
				$title = _x( 'Chats', 'post format archive title', 'wp-rig' );
			}
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		} else {
			$title = __( 'Archives', 'wp-rig' );
		}
		return $title;
	}

	/**
	 * Sets up an options page to be used for Site Theme settings.
	 * Only if ACF is activated.
	 */
	public function acf_op_init() {
		if ( function_exists( 'acf_add_options_page' ) ) {

			acf_add_options_page(
				array(
					'page_title' => 'Theme General Settings',
					'menu_title' => 'Theme Settings',
					'menu_slug'  => 'theme-general-settings',
					'capability' => 'edit_posts',
					'redirect'   => false,
				)
			);

		}

	}


}
