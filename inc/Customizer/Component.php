<?php
/**
 * WP_Rig\WP_Rig\Customizer\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Customizer;

use WP_Rig\WP_Rig\Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use WP_Customize_Manager;
use function add_action;
use function bloginfo;
use function wp_enqueue_script;
use function get_theme_file_uri;

/**
 * Class for managing Customizer integration.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'customizer';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'customize_register', array( $this, 'action_customize_register' ) );
		add_action( 'customize_preview_init', array( $this, 'action_enqueue_customize_preview_js' ) );
		add_action( 'wp_head', array( $this, 'footer_settings_css' ) );

	}

	/**
	 * Adds postMessage support for site title and description, plus a custom Theme Options section.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 */
	public function action_customize_register( WP_Customize_Manager $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'blogname',
				array(
					'selector'        => '.site-title a',
					'render_callback' => function() {
						bloginfo( 'name' );
					},
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'blogdescription',
				array(
					'selector'        => '.site-description',
					'render_callback' => function() {
						bloginfo( 'description' );
					},
				)
			);
		}

		// replace title_tagline with multiple options.
		$wp_customize->remove_control( 'display_header_text' );

		$wp_customize->add_setting(
			'title_tagline_display',
			array(
				'type'      => 'theme_mod',
				'default'   => 'title_tagline',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'title_tagline_display',
			array(
				'type'    => 'radio',
				'section' => 'title_tagline',
				'choices' => array(
					'title_tagline' => __( 'Display Title & Tagline', 'wp-rig' ),
					'title_only'    => __( 'Display Title Only', 'wp-rig' ),
					'tagline_only'  => __( 'Display Tagline Only', 'wp-rig' ),
					'none'          => __( 'Hide Title & Tagline', 'wp-rig' ),
				),
			)
		);

		/**
		 * Theme options.
		 */
		$wp_customize->add_section(
			'theme_options',
			array(
				'title'    => __( 'Theme Options', 'wp-rig' ),
				'priority' => 130, // Before Additional CSS.
			)
		);

		// Add Settings.
		$wp_customize->add_setting(
			'wprig_show_post_author',
			array(
				'default' => '1',
			)
		);
		$wp_customize->add_setting(
			'wprig_show_post_sharing',
			array(
				'default' => '1',
			)
		);

		$wp_customize->add_setting(
			'wprig_excerpt_length',
			array(
				'default' => '35',
			)
		);

		$wp_customize->add_control(
			'wprig_excerpt_length',
			array(
				'label'    => __( 'Post Excerpt Length', 'wp-rig' ),
				'settings' => 'wprig_excerpt_length',
				'section'  => 'theme_options',
				'type'     => 'number',
			)
		);

		$wp_customize->add_control(
			'wprig_show_post_author',
			array(
				'label'    => __( 'Display Post Author on Posts', 'wp-rig' ),
				'settings' => 'wprig_show_post_author',
				'section'  => 'theme_options',
				'type'     => 'checkbox',
				'std'      => '1',
			)
		);

		$wp_customize->add_control(
			'wprig_show_post_sharing',
			array(
				'label'    => __( 'Add Social Sharing on Posts', 'wp-rig' ),
				'settings' => 'wprig_show_post_sharing',
				'section'  => 'theme_options',
				'type'     => 'checkbox',
				'std'      => '1',
			)
		);

		/**
		 * Footer options.
		 */
		$wp_customize->add_section(
			'wprig_footer_section',
			array(
				'title'       => 'Footer Settings',
				'description' => 'Set options for the footer',
				'priority'    => '40',
			)
		);

		// Add Settings.
		$wp_customize->add_setting(
			'wprig_footertop_bg_color',
			array(
				'default' => '#EAEAEA',
			)
		);

		$wp_customize->add_setting(
			'wprig_footertop_text_color',
			array(
				'default' => '#414141',
			)
		);

		$wp_customize->add_setting(
			'wprig_footerlower_bg_color',
			array(
				'default' => '#F2F2F2',
			)
		);

		$wp_customize->add_setting(
			'wprig_footerlower_text_color',
			array(
				'default' => '#414141',
			)
		);

		// Add Controls.
		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'wprig_footertop_bg_color',
				array(
					'label'    => 'Footer Top BG Color',
					'section'  => 'wprig_footer_section',
					'settings' => 'wprig_footertop_bg_color',

				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'wprig_footertop_text_color',
				array(
					'label'    => 'Footer Top Text Color',
					'section'  => 'wprig_footer_section',
					'settings' => 'wprig_footertop_text_color',
				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'wprig_footerlower_bg_color',
				array(
					'label'    => 'Footer Lower BG Color',
					'section'  => 'wprig_footer_section',
					'settings' => 'wprig_footerlower_bg_color',

				)
			)
		);

		$wp_customize->add_control(
			new \WP_Customize_Color_Control(
				$wp_customize,
				'wprig_footerlower_text_color',
				array(
					'label'    => 'Footer Lower Text Color',
					'section'  => 'wprig_footer_section',
					'settings' => 'wprig_footerlower_text_color',
				)
			)
		);
	}


	/**
	 * Adds inline css to style the footer.
	 */
	public function footer_settings_css() {

		$footer_top_bg     = get_theme_mod( 'wprig_footertop_bg_color' );
		$footer_top_text   = get_theme_mod( 'wprig_footertop_text_color' );
		$footer_lower_bg   = get_theme_mod( 'wprig_footerlower_bg_color' );
		$footer_lower_text = get_theme_mod( 'wprig_footerlower_text_color' );

		if ( ! empty( $footer_top_bg ) ) :

			?>
		<style type="text/css" id="diwp-theme-option-css">

			.footer-upper{
				background:<?php echo esc_attr( $footer_top_bg ); ?>;
			}

			.footer-upper,.footer-upper h2, .footer-upper h3, .footer-upper h4, .footer-upper a, .footer-upper a:hover{
				color: <?php echo esc_attr( $footer_top_text ); ?>
			}

			.footer-lower{
				background:<?php echo esc_attr( $footer_lower_bg ); ?>;
			}

			.footer-lower,.footer-lower h2, .footer-lower h3, .footer-lower h4, .footer-lower a, .footer-lower a:hover{
				color: <?php echo esc_attr( $footer_lower_text ); ?>
			}


		</style>

			<?php

		endif;
	}


	/**
	 * Enqueues JavaScript to make Customizer preview reload changes asynchronously.
	 */
	public function action_enqueue_customize_preview_js() {
		wp_enqueue_script(
			'wp-rig-customizer',
			get_theme_file_uri( '/assets/js/customizer.min.js' ),
			array( 'customize-preview' ),
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/customizer.min.js' ) ),
			true
		);
	}
}
