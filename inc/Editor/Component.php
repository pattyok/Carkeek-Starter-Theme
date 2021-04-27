<?php
/**
 * WP_Rig\WP_Rig\Editor\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Editor;

use WP_Rig\WP_Rig\Component_Interface;
use function add_action;
use function add_theme_support;

/**
 * Class for integrating with the block editor.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'editor';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', array( $this, 'action_add_editor_support' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'custom_color_palettes' ) );
	}

	/**
	 * Adds support for various editor features.
	 */
	public function action_add_editor_support() {
		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Add support for default block styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for responsive embeds.
		add_theme_support( 'responsive-embeds' );

		// Add support for wide-aligned images.
		add_theme_support( 'align-wide' );

		// Remove core block patterns.
		remove_theme_support( 'core-block-patterns' );

		/**
		 * Add support for color palettes.
		 *
		 * To preserve color behavior across themes, use these naming conventions:
		 * - Use primary and secondary color for main variations.
		 * - Use `theme-[color-name]` naming standard for standard colors (red, blue, etc).
		 * - Use `custom-[color-name]` for non-standard colors.
		 *
		 * Add the line below to disable the custom color picker in the editor.
		 * add_theme_support( 'disable-custom-colors' );
		 * --color-theme-black: #1c2833;
		 */
		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => __( 'Grey', 'wp-rig' ),
					'slug'  => 'theme-grey',
					'color' => '#c0c0c0',
				),
				array(
					'name'  => __( 'Grey-Light', 'wp-rig' ),
					'slug'  => 'theme-grey-light',
					'color' => '#F2F2F2',
				),
				array(
					'name'  => __( 'Blue', 'wp-rig' ),
					'slug'  => 'theme-blue',
					'color' => '#037bc1',
				),
				array(
					'name'  => __( 'Green', 'wp-rig' ),
					'slug'  => 'theme-green',
					'color' => '#3faf49',
				),
				array(
					'name'  => __( 'Blue Dark', 'wp-rig' ),
					'slug'  => 'theme-blue-dark',
					'color' => '#037bc1',
				),
				array(
					'name'  => __( 'Green Dark', 'wp-rig' ),
					'slug'  => 'theme-green-dark',
					'color' => '#007a4b',
				),
				array(
					'name'  => __( 'White', 'wp-rig' ),
					'slug'  => 'theme-white',
					'color' => '#fff',
				),
				array(
					'name'  => __( 'Black', 'wp-rig' ),
					'slug'  => 'theme-black',
					'color' => '#414141',
				),
			)
		);

		/*
		 * Add support custom font sizes.
		 *
		 * Add the line below to disable the custom color picker in the editor.
		 * add_theme_support( 'disable-custom-font-sizes' );
		 */
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => __( 'Small', 'wp-rig' ),
					'shortName' => __( 'S', 'wp-rig' ),
					'size'      => 16,
					'slug'      => 'small',
				),
				array(
					'name'      => __( 'Medium', 'wp-rig' ),
					'shortName' => __( 'M', 'wp-rig' ),
					'size'      => 25,
					'slug'      => 'medium',
				),
				array(
					'name'      => __( 'Large', 'wp-rig' ),
					'shortName' => __( 'L', 'wp-rig' ),
					'size'      => 31,
					'slug'      => 'large',
				),
				array(
					'name'      => __( 'Larger', 'wp-rig' ),
					'shortName' => __( 'XL', 'wp-rig' ),
					'size'      => 39,
					'slug'      => 'larger',
				),
			)
		);
	}

	/**
	 * For the color palette used by the customizer, use the the same options as above.
	 */
	public function custom_color_palettes() {
		$color_palettes = wp_json_encode(
			array(
				'#c0c0c0',
				'#F2F2F2',
				'#3faf49',
				'#007a4b',
				'#037bc1',
				'#252d65',
				'#fff',
				'#000'
			)
		);
		wp_add_inline_script( 'wp-color-picker', 'jQuery.wp.wpColorPicker.prototype.options.palettes = ' . $color_palettes . ';' );
	}

}
