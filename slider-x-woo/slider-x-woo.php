<?php
/*
	Plugin Name: Slider X Woo
	Plugin URI: https://pluginbazar.com/plugin/slider-x-woo
	Description: Multipurpose slider for your WooCommerce store
	Version: 1.0.0
	Text Domain: slider-x-woo
	Author: Pluginbazar
	Author URI: https://pluginbazar.com/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) || exit;
defined( 'SLIDERXWOO_PLUGIN_URL' ) || define( 'SLIDERXWOO_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
defined( 'SLIDERXWOO_PLUGIN_DIR' ) || define( 'SLIDERXWOO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'SLIDERXWOO_PLUGIN_FILE' ) || define( 'SLIDERXWOO_PLUGIN_FILE', plugin_basename( __FILE__ ) );
defined( 'SLIDERXWOO_TICKET_URL' ) || define( 'SLIDERXWOO_TICKET_URL', 'https://pluginbazar.com/supports/woocommerce-open-close/' );
defined( 'SLIDERXWOO_PLUGIN_NAME' ) || define( 'SLIDERXWOO_PLUGIN_NAME', 'Slider X Woo' );
defined( 'SLIDERXWOO_PLUGIN_LINK' ) || define( 'SLIDERXWOO_PLUGIN_LINK', 'https://pluginbazar.com/plugin/woocommerce-open-close/?add-to-cart=3395' );
defined( 'SLIDERXWOO_DOCS_URL' ) || define( 'SLIDERXWOO_DOCS_URL', 'https://pluginbazar.com/d/woocommerce-open-close/' );
defined( 'SLIDERXWOO_CONTACT_URL' ) || define( 'SLIDERXWOO_CONTACT_URL', 'https://pluginbazar.com/contact/' );
defined( 'SLIDERXWOO_WP_REVIEW_URL' ) || define( 'SLIDERXWOO_WP_REVIEW_URL', 'https://wordpress.org/support/plugin/slider-x-woo/reviews/' );
defined( 'SLIDERXWOO_PLUGIN_VERSION' ) || define( 'SLIDERXWOO_PLUGIN_VERSION', '1.0.0' );

if ( ! function_exists( 'sliderxwoo_is_plugin_active' ) ) {
	function sliderxwoo_is_plugin_active( $plugin ) {
		return ( function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) :
			(
				in_array( $plugin, apply_filters( 'active_plugins', ( array ) get_option( 'active_plugins', array() ) ) ) ||
				( is_multisite() && array_key_exists( $plugin, ( array ) get_site_option( 'active_sitewide_plugins', array() ) ) )
			)
		);
	}
}

if ( ! sliderxwoo_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	return;
}

if ( ! class_exists( 'SLIDERXWOO_Main' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/sdk/classes/class-client.php';
}

/**
 * @global SLIDERXWOO_Slider_base $slider
 */
global $slider;


if ( ! class_exists( 'SLIDERXWOO_Main' ) ) {
	/**
	 * Class SLIDERXWOO_Main
	 */
	class SLIDERXWOO_Main {

		protected static $_instance = null;

		protected static $_script_version = null;

		/**
		 * SLIDERXWOO_Main constructor.
		 */
		function __construct() {

			self::$_script_version = defined( 'WP_DEBUG' ) && WP_DEBUG ? current_time( 'U' ) : SLIDERXWOO_PLUGIN_VERSION;

			$this->define_scripts();
			$this->define_classes_functions();

			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		}

		/**
		 * @return SLIDERXWOO_Main
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}


		/**
		 * Load Textdomain
		 */
		function load_textdomain() {
			load_plugin_textdomain( 'slider-x-woo', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Include Classes and Functions
		 */
		function define_classes_functions() {

			require_once SLIDERXWOO_PLUGIN_DIR . 'includes/classes/class-functions.php';
			require_once SLIDERXWOO_PLUGIN_DIR . 'includes/classes/class-hooks.php';
			require_once SLIDERXWOO_PLUGIN_DIR . 'includes/classes/class-post-meta.php';
			require_once SLIDERXWOO_PLUGIN_DIR . 'includes/classes/class-slider-base.php';
			require_once SLIDERXWOO_PLUGIN_DIR . 'includes/classes/class-columns.php';
			require_once SLIDERXWOO_PLUGIN_DIR . 'includes/functions.php';
		}


		/**
		 * Localize Scripts
		 *
		 * @return mixed|void
		 */
		function localize_scripts() {
			return apply_filters( 'sliderxwoo/filters/localize_scripts', array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'copyText'   => esc_html__( 'Copied !', 'slider-x-woo' ),
				'removeConf' => esc_html__( 'Are you really want to remove this schedule?', 'slider-x-woo' ),
			) );
		}


		/**
		 * Load Front Scripts
		 */
		function front_scripts() {

			// Slick
			wp_enqueue_style( 'slick', SLIDERXWOO_PLUGIN_URL . 'assets/slick.css' );
			wp_enqueue_script( 'slick', plugins_url( '/assets/slick.min.js', __FILE__ ), array( 'jquery' ), self::$_script_version, true );

			wp_enqueue_script( 'sliderxwoo', plugins_url( '/assets/front/js/scripts.js', __FILE__ ), array( 'jquery' ), self::$_script_version, true );
			wp_localize_script( 'sliderxwoo', 'sliderxwoo', $this->localize_scripts() );

			wp_enqueue_style( 'sliderxwoo', SLIDERXWOO_PLUGIN_URL . 'assets/front/css/style.css', array(), self::$_script_version );
			wp_enqueue_style( 'sliderxwoo-tool-tip', SLIDERXWOO_PLUGIN_URL . 'assets/hint.min.css' );
		}


		/**
		 * Load Admin Scripts
		 */
		function admin_scripts() {

			wp_enqueue_script( 'sliderxwoo', plugins_url( '/assets/admin/js/scripts.js', __FILE__ ), array( 'jquery' ), self::$_script_version );
			wp_localize_script( 'sliderxwoo', 'sliderxwoo', $this->localize_scripts() );

			wp_enqueue_style( 'sliderxwoo', SLIDERXWOO_PLUGIN_URL . 'assets/admin/css/style.css', self::$_script_version );
			wp_enqueue_style( 'sliderxwoo-tool-tip', SLIDERXWOO_PLUGIN_URL . 'assets/hint.min.css' );
		}

		/**
		 * Load Scripts
		 */
		function define_scripts() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
		}
	}
}


function pb_sdk_init_slider_x_woo() {

	if ( ! function_exists( 'get_plugins' ) ) {
		include_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	global $sliderxwoo_sdk;

	$sliderxwoo_sdk = new Pluginbazar\Client( esc_html( 'Slider X for WooCommerce' ), 'slider-x-woo', 30, __FILE__ );
	$sliderxwoo_sdk->notifications();

	do_action( 'pb_sdk_init_slider_x_woo', $sliderxwoo_sdk );
}

/**
 * @global \Pluginbazar\Client $sliderxwoo_sdk
 */
global $sliderxwoo_sdk;

pb_sdk_init_slider_x_woo();

SLIDERXWOO_Main::instance();