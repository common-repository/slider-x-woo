<?php

use Pluginbazar\Utils;

/**
 * Base slider class.
 */
class SLIDERXWOO_Slider_base {

	public $id = null;

	public $unique_id = null;

	public $type = null;

	public $theme = null;

	/**
	 * SLIDERXWOO_Slider_base.
	 */
	function __construct( $slider_id = 0 ) {
		if ( $slider_id == 0 || empty( $slider_id ) ) {
			return;
		}

		$this->id        = $slider_id;
		$this->unique_id = uniqid( 'sliderxwoo-slider-' );
		$this->type      = $this->get_meta( '_type', 'product' );
		$this->theme     = $this->get_meta( '_theme', '1' );
	}


	/**
	 * Return slider configuration data
	 *
	 * @return mixed|void
	 */
	function get_slider_configs() {

		$center_mode     = (bool) $this->get_meta( '_center_mode', false );
		$arrows_nav_type = $this->get_meta( '_arrows_nav_type', 'text' );
		$prev_label      = $this->get_meta( '_arrows_label_prev_label', esc_html__( 'Previous', 'slider-x-woo' ) );
		$next_label      = $this->get_meta( '_arrows_label_next_label', esc_html__( 'Next', 'slider-x-woo' ) );
		$prev_icon       = $this->get_meta( '_arrows_label_prev_icon', 'fas fa-angle-left' );
		$next_icon       = $this->get_meta( '_arrows_label_next_icon', 'fas fa-angle-right' );
		$arrows_prev     = "<button class='sliderxwoo-slider-button sliderxwoo-slider-button-prev type-{$arrows_nav_type}'>" . ( 'icon' === $arrows_nav_type ? sprintf( "<i class='fa %s'></i>", $prev_icon ) : $prev_label ) . "</button>";
		$arrows_next     = "<button class='sliderxwoo-slider-button sliderxwoo-slider-button-next type-{$arrows_nav_type}'>" . ( 'icon' === $arrows_nav_type ? sprintf( "<i class='fa %s'></i>", $next_icon ) : $next_label ) . "</button>";

		$configs = array(
			'accessibility'    => true,
			'autoplay'         => (bool) $this->get_meta( '_autoplay', false ),
			'autoplaySpeed'    => (int) $this->get_meta( '_autoplay_speed', 2000 ),
			'arrows'           => (bool) $this->get_meta( '_arrows', true ),
			'prevArrow'        => $arrows_prev,
			'nextArrow'        => $arrows_next,
			'pauseOnFocus'     => (bool) $this->get_meta( '_pause_on_focus', true ),
			'pauseOnHover'     => (bool) $this->get_meta( '_pause_on_hover', true ),
			'pauseOnDotsHover' => (bool) $this->get_meta( '_pause_on_dots_hover', false ),
			'dots'             => (bool) $this->get_meta( '_dots', true ),
			'infinite'         => (bool) $this->get_meta( '_infinite', true ),
			'slidesToShow'     => (int) $this->get_meta( '_slides_to_show', 4 ),
			'slidesToScroll'   => (int) $this->get_meta( '_slides_to_scroll', 1 ),
			'centerMode'       => $center_mode,
		);

		if ( $center_mode ) {
			$configs['focusOnSelect'] = (bool) $this->get_meta( '_focus_on_select', false );
		}


		return apply_filters( 'SLIDERXWOO/Filters/get_slider_configs', $configs );
	}


	/**
	 * Write slider classes with html attribute
	 *
	 * @param string $classes
	 */
	public function slider_classes( $classes = '' ) {

		global $slider;

		$classes   = explode( ' ', $classes );
		$classes[] = 'sliderxwoo-slider';
		$classes[] = $this->unique_id;
		$classes[] = 'slider-wrapper';
		$classes[] = 'type-' . $this->type;
		$classes[] = 'theme-' . $this->theme;

		// Slider title class
		if ( $slider->get_meta( '_slider_title', true ) ) {
			$classes[] = 'has-title';
		}

		// Slide item title class
		if ( $slider->get_meta( '_item_title', true ) ) {
			$classes[] = 'has-item-title';
		}

		printf( 'class="%s"', implode( ' ', apply_filters( 'SLIDERXWOO/Filters/slider_classes', $classes ) ) );
	}


	/**
	 *
	 * @param array $args
	 *
	 * @return WC_Product[]
	 */
	public function get_slider_items( $args = array() ) {

		$slider_items = array();

		if ( 'product' === $this->type ) {
			$slider_items = $this->get_products( $args );
		}

		return apply_filters( 'SLIDERXWOO/Filters/get_slider_items', $slider_items );
	}


	/**
	 *
	 * @param array $args
	 *
	 * @return array|stdClass
	 */
	private function get_products( $args = array() ) {

		$defaults = array(
			'numberposts' => $this->get_meta( '_number_of_products', 10 ),
		);
		$args     = wp_parse_args( $args, $defaults );

		return wc_get_products( $args );
	}


	/**
	 * Return slider title
	 *
	 * @return mixed|void
	 */
	public function get_title() {
		return apply_filters( 'SLIDERXWOO/Filters/get_title', get_the_title( $this->id ) );
	}


	/**
	 * Setter for this class
	 *
	 * @param $prop
	 * @param $value
	 */
	public function set( $prop, $value ) {
		$this->{$prop} = $value;
	}


	/**
	 * Return meta value of slider
	 *
	 * @param string $meta_key
	 * @param string $default
	 *
	 * @return mixed|string|void
	 */
	public function get_meta( $meta_key = '', $default = '' ) {
		return Utils::get_meta( $meta_key, $this->id, $default );
	}
}