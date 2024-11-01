<?php
/**
 * Theme - 10
 *
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

				<?php sliderxwoo_get_slide_item_thumbnail( $product ); ?>

                <div class="slider-item-wrapper">

					<?php sliderxwoo_get_slide_item_title( $product ); ?>

					<?php sliderxwoo_get_slide_item_price( $product ); ?>

					<?php sliderxwoo_get_slide_item_rating( $product ); ?>
                </div>

				<?php sliderxwoo_get_slide_item_cart_btn( $product ); ?>
            </div>
		<?php endforeach; ?>
    </div>
</div>
