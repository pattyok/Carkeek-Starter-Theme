<?php
/**
 * WP_Rig\WP_Rig\Customizer\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Events_Calendar;

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
		return 'events';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'customize_register', array( $this, 'events_customizer' ) ); // add elements to the events header
		add_action( 'tribe_events_after_the_content', array( $this, 'events_details_link' ) );
		add_action( 'tribe_events_after_footer', array( $this, 'events_footer_content' ) );

	}


		/**
		 * Adds an footer to the Events archive page
		 */
	public function events_footer_content() {
		if ( ! in_shortcode() ) {
			?>
		<div id="ck-events-footer" class="ck-events-footer">
			<h2 id="ck-events-footer-headline" class="ck-events-footer-headline">
				<?php echo get_theme_mod( 'events_footer_headline' ); ?>
			</h2>
			<div class="ck-events-footer-content"><?php echo get_theme_mod( 'events_footer_content' ); ?></div>
		</div>

			<?php
		}
	}

	public function events_customizer( $wp_customize ) {
		// Add Section
		$wp_customize->add_section(
			'elf_events_customizer',
			array(
				'title'    => __( 'Events Archive', 'elf' ),
				'priority' => 50,
			)
		);
		// Add Settings
		$wp_customize->add_setting(
			'events_header_image',
			array(
				'transport' => 'refresh',
				'height'    => 325,
			)
		);
		$wp_customize->add_setting(
			'events_header_text',
			array(
				'default'           => 'Upcoming Events',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_setting(
			'events_header_intro',
			array(
				'sanitize_callback' => 'wp_kses_post',
			)
		);
		$wp_customize->add_setting(
			'events_footer_headline',
			array(
				'sanitize_callback' => 'wp_kses_post',
			)
		);
		$wp_customize->add_setting(
			'events_footer_content',
			array(
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_setting(
			'events_no_content',
			array(
				'sanitize_callback' => 'wp_kses_post',
			)
		);
		// Add Controls
		$wp_customize->add_control(
			new \WP_Customize_Image_Control(
				$wp_customize,
				'events_header_image_control',
				array(
					'label'    => __( 'Events Header Image', 'elf' ),
					'section'  => 'elf_events_customizer',
					'settings' => 'events_header_image',
				)
			)
		);

		$wp_customize->add_control(
			'events_header_text',
			array(
				'label'   => esc_html__( 'Page Title', 'theme_slug' ),
				'section' => 'elf_events_customizer',
				'type'    => 'text',
			)
		);

		$wp_customize->add_control(
			'events_header_intro',
			array(
				'label'   => esc_html__( 'Intro Text', 'theme_slug' ),
				'section' => 'elf_events_customizer',
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_control(
			'events_footer_headline',
			array(
				'label'   => esc_html__( 'Footer Headline', 'theme_slug' ),
				'section' => 'elf_events_customizer',
				'type'    => 'text',
			)
		);

		$wp_customize->add_control(
			'events_footer_content',
			array(
				'label'   => esc_html__( 'Footer Content', 'theme_slug' ),
				'section' => 'elf_events_customizer',
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_control(
			'events_no_content',
			array(
				'label'   => esc_html__( 'If no events..', 'theme_slug' ),
				'section' => 'elf_events_customizer',
				'type'    => 'textarea',
			)
		);

		// Selector puts the edit symbol over the element (id needs to be on a wrapper element)
		$wp_customize->selective_refresh->add_partial(
			'events_header_text',
			array(
				'selector' => '#ck-events-header-text', // You can also select a css class
			)
		);

		$wp_customize->selective_refresh->add_partial(
			'events_header_intro',
			array(
				'selector' => '#ck-events-intro', // You can also select a css class
			)
		);

		$wp_customize->selective_refresh->add_partial(
			'events_footer_headline',
			array(
				'selector' => '#ck-events-footer', // You can also select a css class
			)
		);

		$wp_customize->selective_refresh->add_partial(
			'events_no_content',
			array(
				'selector' => '.ck-no-events', // You can also select a css class
			)
		);
	}
}
