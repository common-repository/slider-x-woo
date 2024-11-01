<?php
/**
 * Theme - 5
 */

global $slider, $product;

?>
<div <?php $slider->slider_classes(); ?>>

	<?php sliderxwoo_get_slider_title(); ?>

    <div class="slider-main">
		<?php foreach ( $slider->get_slider_items() as $product ) : ?>
            <div class="slider-item">
                <div class="single-slider-item">

					<?php sliderxwoo_get_slide_item_thumbnail( $product ); ?>

                    <div class="slider-content">
                        <div class="slider-info">
							<?php sliderxwoo_get_slide_item_title( $product ); ?>

							<?php sliderxwoo_get_slide_item_desc( $product ); ?>

							<?php sliderxwoo_get_slide_item_price( $product ); ?>

							<?php sliderxwoo_get_slide_item_rating( $product ); ?>
                        </div>

						<?php sliderxwoo_get_slide_item_cart_btn( $product ); ?>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
</div>