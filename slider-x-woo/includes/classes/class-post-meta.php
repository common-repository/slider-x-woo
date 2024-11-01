<?php
/*
* @Author 		pluginbazar
* Copyright: 	2015 pluginbazar
*/

use \Pluginbazar\Utils;

defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'SLIDERXWOO_Post_meta' ) ) {
	/**
	 * Class SLIDERXWOO_Post_meta
	 */
	class SLIDERXWOO_Post_meta {

		/**
		 * SLIDERXWOO_Post_meta constructor.
		 */
		function __construct() {

			$this->generate_slider_meta_box();

			add_action( 'admin_print_footer_scripts', array( $this, 'add_footer_script' ) );
			add_action( 'admin_menu', array( $this, 'remove_publish_meta_box' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_sidebar_meta_box' ) );
		}

		/**
		 * Display sidebar meta box
		 *
		 * @param WP_Post $post
		 */
		function render_sidebar_meta_box( WP_Post $post ) {

			$post_id          = $post->ID;
			$post_type        = $post->post_type;
			$post_type_object = get_post_type_object( $post_type );
			$can_publish      = current_user_can( $post_type_object->cap->publish_posts );

//			wp_nonce_field( 'woc_nonce', 'woc_nonce_val' );

			echo '<div class="sliderxwoo-sidebar">';

			echo '<div class="sliderxwoo-sidebar-buttons">';

			if ( 'auto-draft' != $post->post_status ) {
				echo '<a class="action-button sliderxwoo-new-item hint--top" aria-label="' . esc_html__( 'Create new slider', 'slider-x-woo' ) . '" href="post-new.php?post_type=' . $post_type . '"><span>+</span></a>';
			}

			if ( ! in_array( $post->post_status, array( 'publish', 'future', 'private' ), true ) || 0 === $post_id ) {
				if ( $can_publish ) :
					if ( ! empty( $post->post_date_gmt ) && time() < strtotime( $post->post_date_gmt . ' +0000' ) ) :
						?>
                        <input name="original_publish" type="hidden" id="original_publish"
                               value="<?php echo esc_attr_x( 'Schedule', 'post action/button label' ); ?>"/>
						<?php submit_button( _x( 'Schedule', 'post action/button label' ), 'primary large', 'publish', false ); ?>
					<?php
					else :
						?>
                        <input name="original_publish" type="hidden" id="original_publish"
                               value="<?php esc_attr_e( 'Publish' ); ?>"/>
						<?php submit_button( __( 'Publish' ), 'primary large', 'publish', false ); ?>
					<?php
					endif;
				else :
					?>
                    <input name="original_publish" type="hidden" id="original_publish"
                           value="<?php esc_attr_e( 'Submit for Review' ); ?>"/>
					<?php submit_button( __( 'Submit for Review' ), 'primary large', 'publish', false ); ?>
				<?php
				endif;
			} else {
				?>
                <input name="original_publish" type="hidden" id="original_publish"
                       value="<?php esc_attr_e( 'Update' ); ?>"/>
				<?php submit_button( __( 'Save' ), 'primary large', 'save', false, array( 'id' => 'publish' ) ); ?>
				<?php
			}

			if ( 'trash' != $post->post_status ) {
				echo '<a class="action-button sliderxwoo-trash-item hint--top" aria-label="' . esc_html__( 'Move to trash', 'slider-x-woo' ) . '" href="' . get_delete_post_link( $post_id ) . '"><span>-</span></a>';
			}

			echo '</div>'; // .sliderxwoo-sidebar-buttons


			echo '<div class="sliderxwoo-shortcodes">';

			echo '<div class="sliderxwoo-shortcode shortcode-post">';
			echo '<h3>' . esc_html__( 'Shortcode', 'slider-x-woo' ) . '</h3>';
			echo '<p>' . esc_html__( 'Copy this shortcode and use it in posts, pages etc.', 'slider-x-woo' ) . '</p>';
			echo '<div class="shortcode hint--top" aria-label="' . esc_html__( 'Click here to copy', 'slider-x-woo' ) . '">[sliderxwoo_slider id="' . esc_attr( $post_id ) . '"]</div>';
			echo '</div>'; // .shortcode-post

			echo '<div class="sliderxwoo-shortcode shortcode-template">';
			echo '<h3>' . esc_html__( 'Shortcode Template', 'slider-x-woo' ) . '</h3>';
			echo '<p>' . esc_html__( 'Use this code in your template file.', 'slider-x-woo' ) . '</p>';
			echo '<div class="shortcode hint--top" aria-label="' . esc_html__( 'Click here to copy', 'slider-x-woo' ) . '">' . esc_html( "<?php echo do_shortcode( '[sliderxwoo_slider id=\"{$post_id}\"]' ); ?>" ) . '</div>';
			echo '</div>'; // .shortcode-template

			echo '</div>'; // .sliderxwoo-shortcodes

			echo '</div>'; // .sliderxwoo-sidebar
		}


		/**
		 * Register sidebar meta box for sliderxwoo post type.
		 *
		 * @param $post_type
		 */
		function add_sidebar_meta_box( $post_type ) {
			if ( 'sliderxwoo' == $post_type ) {
				add_meta_box( 'slider_sidebar_meta_box', ' ', array( $this, 'render_sidebar_meta_box' ), $post_type, 'side', 'high' );
			}
		}


		/**
		 * Remove publish metabox from sliderxwoo post type.
		 */
		public function remove_publish_meta_box() {
			remove_meta_box( 'submitdiv', 'sliderxwoo', 'side' );
		}


		/**
		 * Add footer script for sliderxwoo post type to handle auto-save.
		 */
		function add_footer_script() {
			if ( 'sliderxwoo' == get_post_type() ) {
				?>
                <script>
                    jQuery(document).ready(function () {
                        if (typeof window.wp.autosave !== 'undefined') {
                            window.wp.autosave.server.postChanged = false;
                        }
                    });
                </script>
				<?php
			}
		}


		/**
		 * Generate meta box for slider data
		 */
		function generate_slider_meta_box() {

			$prefix = 'pb_meta_fields';

			/**
			 * Create a metabox for product slider.
			 */
			PBSettings::createMetabox(
				$prefix,
				array(
					'title'     => __( 'Slider Options', 'slider-x-woo' ),
					'post_type' => 'sliderxwoo',
					'data_type' => 'unserialize',
					'context'   => 'normal',
					'nav'       => 'inline',
					'preview'   => true,
				)
			);

			/**
			 * General Settings section.
			 */
			PBSettings::createSection( $prefix,
				array(
					'title'  => __( 'General Settings', 'slider-x-woo' ),
					'icon'   => 'fa fa-cog',
					'fields' => array(
						array(
							'id'       => '_type',
							'type'     => 'button_set',
							'title'    => esc_html__( 'Slider Type', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Select slider type.', 'slider-x-woo' ),
							'options'  => array(
								'product'  => array(
									'label' => esc_html__( 'Product', 'slider-x-woo' ),
								),
								'category' => array(
									'label'        => esc_html__( 'Category', 'slider-x-woo' ),
									'availability' => 'upcoming',
								),
								'coupon'   => array(
									'label'        => esc_html__( 'Coupon', 'slider-x-woo' ),
									'availability' => 'upcoming',
								),
							),
							'default'  => 'product',
						),
						array(
							'id'       => '_slides_to_show',
							'type'     => 'spinner',
							'title'    => esc_html__( 'Slides to show', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Number of slide items to show.', 'slider-x-woo' ),
							'default'  => 4,
							'step'     => 1,
							'min'      => 1,
							'max'      => 20,
						),
						array(
							'id'       => '_slides_to_scroll',
							'type'     => 'spinner',
							'title'    => esc_html__( 'Slider to scroll', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Number of slides to scroll at a time.', 'slider-x-woo' ),
							'default'  => 1,
							'step'     => 1,
							'min'      => 1,
							'max'      => 10,
						),
						array(
							'id'      => 'subheading_products_query',
							'type'    => 'subheading',
							'content' => esc_html__( 'Products Query', 'slider-x-woo' ),
						),
						array(
							'id'       => '_number_of_products',
							'type'     => 'spinner',
							'title'    => esc_html__( 'Number of products', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Total number of products to display.', 'slider-x-woo' ),
							'default'  => 10,
							'step'     => 1,
							'min'      => 1,
						),
					),
				)
			);

			PBSettings::createSection( $prefix,
				array(
					'title'  => esc_html__( 'Slider Settings', 'slider-x-woo' ),
					'icon'   => 'fa fa-cogs',
					'fields' => array(
						array(
							'id'      => '_autoplay',
							'type'    => 'switcher',
							'title'   => esc_html__( 'Autoplay', 'slider-x-woo' ),
							'label'   => esc_html__( 'Enable or disable autoplay of the slider.', 'slider-x-woo' ),
							'default' => false,
						),
						array(
							'id'       => '_autoplay_speed',
							'type'     => 'spinner',
							'title'    => esc_html__( 'Speed', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Speed of slider when autoplay is enabled.', 'slider-x-woo' ),
							'default'  => 2000,
							'unit'     => esc_attr( 'ms' ),
							'step'     => 100,
							'min'      => 100,
							'max'      => 10000,
						),
						array(
							'id'      => '_pause_on_focus',
							'type'    => 'switcher',
							'title'   => esc_html__( 'Pause settings', 'slider-x-woo' ),
							'label'   => esc_html__( 'Pause the slider when focus on it.', 'slider-x-woo' ),
							'default' => true,
						),
						array(
							'id'      => '_pause_on_hover',
							'type'    => 'switcher',
							'title'   => ' ',
							'class'   => 'padding-top-none',
							'label'   => esc_html__( 'Pause the slider when hover on it.', 'slider-x-woo' ),
							'default' => false,
						),
						array(
							'id'      => '_pause_on_dots_hover',
							'type'    => 'switcher',
							'title'   => ' ',
							'class'   => 'padding-top-none',
							'label'   => esc_html__( 'Pause the slider when hover on the dots or navigation.', 'slider-x-woo' ),
							'default' => false,
						),
						array(
							'id'      => '_infinite',
							'type'    => 'switcher',
							'title'   => esc_html__( 'Infinite loop', 'slider-x-woo' ),
							'label'   => esc_html__( 'Slider will keep sliding from start item after finish.', 'slider-x-woo' ),
							'default' => true,
						),
						array(
							'id'      => 'subheading_navigation_controls',
							'type'    => 'subheading',
							'content' => esc_html__( 'Navigation Controls', 'slider-x-woo' ),
						),
						array(
							'id'      => '_arrows',
							'type'    => 'switcher',
							'title'   => esc_html__( 'Arrows', 'slider-x-woo' ),
							'label'   => esc_html__( 'Enable or disable slider arrows.', 'slider-x-woo' ),
							'default' => true,
						),
						array(
							'id'      => '_arrows_nav_type',
							'type'    => 'button_set',
							'title'   => esc_html__( 'Arrows type', 'slider-x-woo' ),
							'options' => array(
								'text' => array(
									'label' => esc_html__( 'Text', 'slider-x-woo' ),
								),
								'icon' => array(
									'label' => esc_html__( 'Icon', 'slider-x-woo' ),
								),
							),
							'default' => 'text'
						),
						array(
							'id'         => '_arrows_label_prev_label',
							'type'       => 'text',
							'class'      => 'text-field-small',
							'title'      => esc_html__( 'Previous arrow', 'slider-x-woo' ),
							'subtitle'   => esc_html__( 'Set text for the previous arrow label.', 'slider-x-woo' ),
							'default'    => esc_html__( 'Prev', 'slider-x-woo' ),
							'dependency' => array( '_arrows_nav_type', '==', 'text' ),
						),
						array(
							'id'         => '_arrows_label_next_label',
							'type'       => 'text',
							'class'      => 'text-field-small',
							'title'      => esc_html__( 'Next arrow', 'slider-x-woo' ),
							'subtitle'   => esc_html__( 'Set text for the next arrow label.', 'slider-x-woo' ),
							'default'    => esc_html__( 'Next', 'slider-x-woo' ),
							'dependency' => array( '_arrows_nav_type', '==', 'text' ),
						),
						array(
							'id'         => '_arrows_label_prev_icon',
							'type'       => 'icon',
							'title'      => esc_html__( 'Previous arrow', 'slider-x-woo' ),
							'subtitle'   => esc_html__( 'Set icon for the previous arrow label.', 'slider-x-woo' ),
							'default'    => 'fas fa-angle-left',
							'dependency' => array( '_arrows_nav_type', '==', 'icon' ),
						),
						array(
							'id'         => '_arrows_label_next_icon',
							'type'       => 'icon',
							'title'      => esc_html__( 'Next arrow', 'slider-x-woo' ),
							'subtitle'   => esc_html__( 'Set icon for the next arrow label.', 'slider-x-woo' ),
							'default'    => 'fas fa-angle-right',
							'dependency' => array( '_arrows_nav_type', '==', 'icon' ),
						),
						array(
							'id'      => '_dots',
							'type'    => 'switcher',
							'title'   => esc_html__( 'Slider dots', 'slider-x-woo' ),
							'label'   => esc_html__( 'Enable or disable dots for this slider.', 'slider-x-woo' ),
							'default' => true,
						),

						array(
							'id'      => 'subheading_miscellaneous',
							'type'    => 'subheading',
							'content' => esc_html__( 'Miscellaneous', 'slider-x-woo' ),
						),
						array(
							'id'       => '_center_mode',
							'type'     => 'switcher',
							'title'    => esc_html__( 'Center Mode', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Control centered view with prev/next slides.', 'slider-x-woo' ),
							'label'    => esc_html__( 'You can use with odd numbered slidesToShow counts.', 'slider-x-woo' ),
							'default'  => false,
						),
						array(
							'id'       => '_focus_on_select',
							'type'     => 'switcher',
							'title'    => esc_html__( 'Focus on Select', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Control focus on selected/clicked item.', 'slider-x-woo' ),
							'label'    => esc_html__( 'To make this working, you must enable "Center Mode".', 'slider-x-woo' ),
							'default'  => false,
						),
					),
				)
			);

			PBSettings::createSection( $prefix,
				array(
					'title'  => esc_html__( 'Display Settings', 'slider-x-woo' ),
					'icon'   => 'fa fa-desktop',
					'fields' => array(
						array(
							'id'       => '_slider_title',
							'type'     => 'switcher',
							'title'    => esc_html__( 'Slider Title', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Control slider title visibility.', 'slider-x-woo' ),
							'label'    => esc_html__( 'Hides the title for this slider', 'slider-x-woo' ),
							'default'  => true,
						),
						// Item Thumbnail
						array(
							'id'      => 'subheading_item_thumb',
							'type'    => 'subheading',
							'content' => esc_html__( 'Item Thumbnail', 'slider-x-woo' ),
						),
						array(
							'id'       => '_item_thumb',
							'type'     => 'switcher',
							'title'    => esc_html__( 'Slider Item Thumbnail', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Control item thumb visibility.', 'slider-x-woo' ),
							'label'    => esc_html__( 'Display or hide the thumbnail for all the slide items.', 'slider-x-woo' ),
							'default'  => true,
						),
						// Item Title
						array(
							'id'      => 'subheading_item_title',
							'type'    => 'subheading',
							'content' => esc_html__( 'Item Title', 'slider-x-woo' ),
						),
						array(
							'id'       => '_item_title',
							'type'     => 'switcher',
							'title'    => esc_html__( 'Slider Item Title', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Control item title visibility.', 'slider-x-woo' ),
							'label'    => esc_html__( 'Display or hide the title for all the slide items.', 'slider-x-woo' ),
							'default'  => false,
						),
						array(
							'id'         => '_title_limit',
							'type'       => 'switcher',
							'title'      => esc_html__( 'Title Word Limit', 'slider-x-woo' ),
							'subtitle'   => esc_html__( 'Control item title word limit.', 'slider-x-woo' ),
							'label'      => esc_html__( 'By default full title will be displayed.', 'slider-x-woo' ),
							'default'    => false,
							'dependency' => array( '_item_title', '==', 'true' ),
						),
						array(
							'id'         => '_title_limit_length',
							'type'       => 'spinner',
							'title'      => esc_html__( 'Title Word Length', 'slider-x-woo' ),
							'subtitle'   => esc_html__( 'Number of word counts for the item title.', 'slider-x-woo' ),
							'default'    => 3,
							'min'        => 1,
							'step'       => 1,
							'dependency' => array( '_title_limit', '==', 'true' ),
						),
						// Item Description
						array(
							'id'      => 'subheading_item_desc',
							'type'    => 'subheading',
							'content' => esc_html__( 'Item Description', 'slider-x-woo' ),
						),
						array(
							'id'       => '_item_desc',
							'type'     => 'switcher',
							'title'    => esc_html__( 'Slider Item Description', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Control item description visibility.', 'slider-x-woo' ),
							'label'    => esc_html__( 'Display or hide the description for all the slide items.', 'slider-x-woo' ),
							'default'  => false,
						),
						array(
							'id'         => '_desc_limit_length',
							'type'       => 'spinner',
							'title'      => esc_html__( 'Description Word Length', 'slider-x-woo' ),
							'subtitle'   => esc_html__( 'Number of word counts for the item description.', 'slider-x-woo' ),
							'default'    => 10,
							'min'        => 5,
							'step'       => 1,
							'dependency' => array( '_item_desc', '==', 'true' ),
						),
						// Item Pricing
						array(
							'id'      => 'subheading_item_price',
							'type'    => 'subheading',
							'content' => esc_html__( 'Item Pricing', 'slider-x-woo' ),
						),
						array(
							'id'       => '_item_price',
							'type'     => 'switcher',
							'title'    => esc_html__( 'Slider Item Price', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Control item price visibility.', 'slider-x-woo' ),
							'label'    => esc_html__( 'Display or hide the pricing for all the slide items.', 'slider-x-woo' ),
							'default'  => true,
						),
						// Item Rating
						array(
							'id'      => 'subheading_item_rating',
							'type'    => 'subheading',
							'content' => esc_html__( 'Item Rating', 'slider-x-woo' ),
						),
						array(
							'id'       => '_item_rating',
							'type'     => 'switcher',
							'title'    => esc_html__( 'Slider Item Rating', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Control item rating visibility.', 'slider-x-woo' ),
							'label'    => esc_html__( 'Display or hide the rating for all the slide items.', 'slider-x-woo' ),
							'default'  => true,
						),
						// Item Rating
						array(
							'id'      => 'subheading_item_cart_btn',
							'type'    => 'subheading',
							'content' => esc_html__( 'Item Cart Button / Button', 'slider-x-woo' ),
						),
						array(
							'id'       => '_item_cart_btn',
							'type'     => 'switcher',
							'title'    => esc_html__( 'Item Cart Button', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Control item add-to-cart button visibility.', 'slider-x-woo' ),
							'label'    => esc_html__( 'Display or hide the add-to-cart button for all the slide items.', 'slider-x-woo' ),
							'default'  => true,
						),
					),
				)
			);

			PBSettings::createSection( $prefix,
				array(
					'title'  => esc_html__( 'Slider Styling', 'slider-x-woo' ),
					'icon'   => 'fa fa-cog',
					'fields' => array(
						array(
							'id'       => '_theme',
							'type'     => 'select',
							'title'    => esc_html__( 'Slider theme', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Select theme for this slider.', 'slider-x-woo' ) . ' ' . esc_html__( 'You can checkout all the themes from here.', 'slider-x-woo' ),
							'options'  => array(
								'1'  => array(
									'label' => esc_html__( 'Theme 1', 'slider-x-woo' ),
								),
								'2'  => array(
									'label' => esc_html__( 'Theme 2', 'slider-x-woo' ),
								),
								'3'  => array(
									'label' => esc_html__( 'Theme 3', 'slider-x-woo' ),
								),
								'4'  => array(
									'label' => esc_html__( 'Theme 4', 'slider-x-woo' ),
								),
								'5'  => array(
									'label' => esc_html__( 'Theme 5', 'slider-x-woo' ),
								),
								'6'  => array(
									'label' => esc_html__( 'Theme 6', 'slider-x-woo' ),
								),
								'7'  => array(
									'label' => esc_html__( 'Theme 7', 'slider-x-woo' ),
								),
								'8'  => array(
									'label' => esc_html__( 'Theme 8', 'slider-x-woo' ),
								),
								'9'  => array(
									'label' => esc_html__( 'Theme 9', 'slider-x-woo' ),
								),
								'10' => array(
									'label' => esc_html__( 'Theme 10', 'slider-x-woo' ),
								),
								'x'  => array(
									'label'        => esc_html__( '50+ themes are coming soon', 'slider-x-woo' ),
									'availability' => 'upcoming',
								),
							),
						),
						array(
							'id'      => 'subheading_typography',
							'type'    => 'subheading',
							'content' => esc_html__( 'Typography', 'slider-x-woo' ),
						),
						array(
							'id'       => '_typo_slider_title',
							'type'     => 'typography',
							'title'    => esc_html__( 'Slider title', 'slider-x-woo' ),
							'subtitle' => esc_html__( 'Configure typography options for slider title.', 'slider-x-woo' ),
//							'default'  => true,
						),
						array(
							'id'       => 'woc_timer_display_on',
							'title'    => esc_html__( 'Display countdown timer', 'woc-open-close' ),
							'subtitle' => esc_html__( 'Select the places where you want to display the countdown timer on your shop.', 'woc-open-close' ),
							'desc'     => esc_html__( 'When your shop is closed then it will show how much time left for your shop to open, and vice verse.', 'woc-open-close' ),
							'type'     => 'select',
							'chosen'   => true,
							'multiple' => true,
							'settings' => array(
								'width' => '50%',
							),
							'options'  => array(
								'before_cart_table'    => esc_html__( 'Before cart table on Cart page', 'woc-open-close' ),
								'after_cart_table'     => esc_html__( 'After cart table on Cart page', 'woc-open-close' ),
								'before_cart_total'    => esc_html__( 'Before cart total on Cart page', 'woc-open-close' ),
								'after_cart_total'     => esc_html__( 'After cart total on Cart page', 'woc-open-close' ),
								'before_checkout_form' => esc_html__( 'Before checkout form on Checkout Page', 'woc-open-close' ),
								'after_checkout_form'  => esc_html__( 'After checkout form on Checkout Page', 'woc-open-close' ),
								'before_order_review'  => esc_html__( 'Before order review on Checkout Page', 'woc-open-close' ),
								'after_order_review'   => esc_html__( 'After order review on Checkout Page', 'woc-open-close' ),
								'before_cart_single'   => esc_html__( 'Before cart button on Single Product Page', 'woc-open-close' ),
								'top_on_myaccount'     => esc_html__( 'Top on My-Account Page', 'woc-open-close' ),
							),
						),


					)
				)
			);
		}


		function get_slider_meta_fields() {

			$meta_sections['general_settings'] = array(
				'title'        => __( 'General Settings', 'slider-x-woo' ),
				'icon'         => 'fa fa-cog',
				'sub_sections' => array(
					array(
						'subheading' => '',
						'fields'     => array(
							array(
								'id'       => '_type',
								'type'     => 'button_set',
								'title'    => esc_html__( 'Slider Type', 'slider-x-woo' ),
								'subtitle' => esc_html__( 'Select slider type.', 'slider-x-woo' ),
								'options'  => array(
									'product'  => array(
										'label' => esc_html__( 'Product', 'slider-x-woo' ),
									),
									'category' => array(
										'label'        => esc_html__( 'Category', 'slider-x-woo' ),
										'availability' => 'upcoming',
									),
									'coupon'   => array(
										'label'        => esc_html__( 'Coupon', 'slider-x-woo' ),
										'availability' => 'upcoming',
									),
								),
								'default'  => 'product',
							),
						),
					),
				),
			);


			$meta_sections['slider_settings'] = array(
				'title'        => esc_html__( 'Slider Settings', 'slider-x-woo' ),
				'icon'         => 'fa fa-cogs',
				'sub_sections' => array(
					array(
						'subheading' => '',
						'fields'     => array(
							array(
								'id'       => '_rows',
								'type'     => 'spinner',
								'title'    => esc_html__( 'Rows', 'slider-x-woo' ),
								'subtitle' => esc_html__( 'Specify how many rows will show in this slider.', 'slider-x-woo' ),
								'default'  => 1,
								'step'     => 1,
								'min'      => 1,
								'max'      => 5,
							),
							array(
								'id'       => '_slides_per_row',
								'type'     => 'spinner',
								'title'    => esc_html__( 'Slide items per row', 'slider-x-woo' ),
								'subtitle' => esc_html__( 'In each row, the number of slide items to show..', 'slider-x-woo' ),
								'default'  => 3,
								'step'     => 1,
								'min'      => 1,
								'max'      => 10,
							),
							array(
								'id'       => '_slides_to_show',
								'type'     => 'spinner',
								'title'    => esc_html__( 'Slide items to show', 'slider-x-woo' ),
								'subtitle' => esc_html__( 'Set the number of slide items visible in the slider.', 'slider-x-woo' ),
								'default'  => 3,
								'step'     => 1,
								'min'      => 1,
								'max'      => 10,
							),
							array(
								'id'       => '_rows',
								'type'     => 'spinner',
								'title'    => esc_html__( 'Slide items to scroll', 'slider-x-woo' ),
								'subtitle' => esc_html__( 'Number of items that scroll at a single slide.', 'slider-x-woo' ),
								'default'  => 1,
								'step'     => 1,
								'min'      => 1,
								'max'      => 10,
							),
						),
					),
					array(
						'subheading' => esc_html__( 'Autoplay', 'slider-x-woo' ),
						'fields'     => array(),
					),
				),
			);
		}
	}
}

new SLIDERXWOO_Post_meta();