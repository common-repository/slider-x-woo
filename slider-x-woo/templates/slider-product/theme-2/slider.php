<?php
/**
 * Theme - 2
 * @version 1.0.0
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

global $slider, $product;

?>
<div <?php $slider->slider_classes(); ?>>
	<?php sliderxwoo_get_slider_title(); ?>
    <div class="slider-main">
		<?php foreach ( $slider->get_slider_items() as $product ) : ?>
            <div class="slider-item">
                <div class="slider-single-item">

					<?php sliderxwoo_get_slide_item_thumbnail( $product ); ?>

                    <div class="item-content-wrapper">
						<?php sliderxwoo_get_slide_item_title( $product ); ?>

						<?php sliderxwoo_get_slide_item_price( $product ); ?>

						<?php sliderxwoo_get_slide_item_rating( $product ); ?>

						<?php sliderxwoo_get_slide_item_cart_btn( $product ); ?>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
</div>