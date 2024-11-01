<?php
/**
 * Class Hooks
 *
 * @author Pluginbazar
 */

use Pluginbazar\Utils;

if ( ! class_exists( 'SLIDERXWOO_Hooks' ) ) {
	/**
	 * Class SLIDERXWOO_Hooks
	 */
	class SLIDERXWOO_Hooks {

		protected static $_instance = null;

		/**
		 * SLIDERXWOO_Hooks constructor.
		 */
		function __construct() {

			add_action( 'init', array( $this, 'register_everything' ) );
			add_filter( 'admin_footer_text', array( $this, 'customize_admin_footer_text' ), 1 );
			add_filter( 'post_updated_messages', array( $this, 'filter_update_messages' ), 10, 1 );

			add_action( 'in_admin_header', array( $this, 'render_admin_loader' ), 0 );
			add_action( 'sliderxwoo_after_slider', array( $this, 'render_slider_scripts' ), 10, 1 );
		}


		/**
		 * @return SLIDERXWOO_Hooks
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}


		/**
		 * Render slider scripts
		 *
		 * @param SLIDERXWOO_Slider_base $slider
		 */
		function render_slider_scripts( SLIDERXWOO_Slider_base $slider ) {

			$slider_configs = $slider->get_slider_configs();
			$slider_configs = preg_replace( '/"([^"]+)"\s*:\s*/', '$1:', json_encode( $slider_configs, JSON_HEX_QUOT | JSON_HEX_TAG ) );

			?>
            <script>
                jQuery(document).ready(function () {
                    jQuery('.<?php echo esc_attr( $slider->unique_id ); ?> .slider-main').slick(<?php echo wp_kses_post( $slider_configs ); ?>);
                });
            </script>
			<?php
		}


		/**
		 * Render preloader in Admin
		 */
		function render_admin_loader() {

			global $current_screen;

			if ( $current_screen->post_type == 'sliderxwoo' ) {
				printf( '<div class="sliderxwoo-loader-wrap"><div class="sliderxwoo-loader"></div></div>' );
			}
		}


		/**
		 * Update messages for this post type
		 *
		 * @param array $messages
		 *
		 * @return array
		 */
		function filter_update_messages( $messages = array() ) {

			if ( get_post_type() === 'sliderxwoo' ) {

				if ( isset( $messages['post'][1] ) ) {
					$messages['post'][1] = esc_html__( 'Slider data has been updated successfully', 'slider-x-woo' );
				}

				if ( isset( $messages['post'][6] ) ) {
					$messages['post'][6] = esc_html__( 'Slider data has been published successfully', 'slider-x-woo' );
				}
			}

			return $messages;
		}

		/**
		 * Add custom footer text.
		 *
		 * @param $text
		 *
		 * @return mixed|string
		 */
		public function customize_admin_footer_text( $text ) {

			global $current_screen;

			if ( 'sliderxwoo' === $current_screen->post_type || 'sp_wps_shortcodes_page_wps_settings' === $current_screen->id ) {
				$text = sprintf( wp_kses_post( 'If you like <strong>%s</strong> please leave us a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating.' ), SLIDERXWOO_PLUGIN_NAME, SLIDERXWOO_WP_REVIEW_URL );
			}

			return $text;
		}


		/**
		 * Render slider shortcode
		 *
		 * @param array $atts
		 *
		 * @return false|string
		 */
		function render_slider( $atts = array() ) {

			global $slider;

			$atts         = shortcode_atts( array(
				'id'    => '',
				'theme' => '',
			), $atts, 'sliderxwoo_slider' );
			$slider_id    = Utils::get_args_option( 'id', $atts );
			$slider_theme = Utils::get_args_option( 'theme', $atts );
			$slider       = sliderxwoo_get_slider( $slider_id );

			if ( ! empty( $slider_theme ) ) {
				$slider->set( 'theme', $slider_theme );
			}

			$template            = sprintf( 'slider-%s/theme-%s/slider.php', $slider->type, $slider->theme );
			$template_styles_dir = sprintf( '%stemplates/slider-%s/theme-%s/css/style.css', SLIDERXWOO_PLUGIN_DIR, $slider->type, $slider->theme );
			$template_styles_url = sprintf( '%stemplates/slider-%s/theme-%s/css/style.css', SLIDERXWOO_PLUGIN_URL, $slider->type, $slider->theme );

			if ( ! file_exists( $template_styles_dir ) ) {
				return 'CSS file not found for theme - ' . $slider->theme;
			}

			ob_start();

			// Template
			sliderxwoo_get_template( apply_filters( 'SLIDERXWOO/Filters/slider_template', $template, $slider ) );

			// Styles
			wp_enqueue_style( sprintf( 'sliderxwoo-theme-%s', $slider->theme ), apply_filters( 'SLIDERXWOO/Filters/slider_template_styles', $template_styles_url, $slider ) );

			do_action( 'sliderxwoo_after_slider', $slider );

			return ob_get_clean();
		}


		/**
		 * Register Post Types and Settings
		 */
		function register_everything() {

			global $sliderxwoo_sdk;

			/**
			 * Register Post Types
			 */
			$sliderxwoo_sdk->utils()->register_post_type( 'sliderxwoo', array(
				'singular'            => esc_html__( 'Slider', 'slider-x-woo' ),
				'plural'              => esc_html__( 'All Sliders', 'slider-x-woo' ),
				'labels'              => array(
					'menu_name' => esc_html__( 'Slider X', 'slider-x-woo' ),
				),
				'menu_icon'           => 'dashicons-slides',
				'supports'            => array( 'title' ),
				'public'              => false,
				'exclude_from_search' => true,
			) );

			/**
			 * Register shortcodes
			 */
			$sliderxwoo_sdk->utils()->register_shortcode( 'sliderxwoo_slider', array( $this, 'render_slider' ) );
		}
	}
}

SLIDERXWOO_Hooks::instance();