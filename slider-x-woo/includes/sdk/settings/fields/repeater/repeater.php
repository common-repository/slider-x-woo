<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: repeater
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'PBSettings_Field_repeater' ) ) {
	class PBSettings_Field_repeater extends PBSettings_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {

			$args = wp_parse_args( $this->field, array(
				'max'             => 0,
				'min'             => 0,
				'button_title'    => '<i class="fas fa-plus-circle"></i>',
				'disable_actions' => array(),
			) );

			if ( preg_match( '/' . preg_quote( '[' . $this->field['id'] . ']' ) . '/', $this->unique ) ) {

				echo '<div class="pbsettings-notice pbsettings-notice-danger">' . esc_html__( 'Error: Field ID conflict.' ) . '</div>';

			} else {

				echo $this->field_before();

				echo '<div class="pbsettings-repeater-item pbsettings-repeater-hidden" data-depend-id="' . esc_attr( $this->field['id'] ) . '">';
				echo '<div class="pbsettings-repeater-content">';
				foreach ( $this->field['fields'] as $field ) {

					$field_default = ( isset( $field['default'] ) ) ? $field['default'] : '';
					$field_unique  = ( ! empty( $this->unique ) ) ? $this->unique . '[' . $this->field['id'] . '][0]' : $this->field['id'] . '[0]';

					PBSettings::field( $field, $field_default, '___' . $field_unique, 'field/repeater' );

				}
				echo '</div>';
				echo '<div class="pbsettings-repeater-helper">';
				echo '<div class="pbsettings-repeater-helper-inner">';

				if ( ! in_array( 'sort', $args['disable_actions'] ) ) {
					echo '<i class="pbsettings-repeater-sort fas fa-arrows-alt"></i>';
				}

				if ( ! in_array( 'clone', $args['disable_actions'] ) ) {
					echo '<i class="pbsettings-repeater-clone far fa-clone"></i>';
				}

				if ( ! in_array( 'remove', $args['disable_actions'] ) ) {
					echo '<i class="pbsettings-repeater-remove pbsettings-confirm fas fa-times" data-confirm="' . esc_html__( 'Are you sure to delete this item?' ) . '"></i>';
				}

				echo '</div>';
				echo '</div>';
				echo '</div>';

				echo '<div class="pbsettings-repeater-wrapper pbsettings-data-wrapper" data-field-id="[' . esc_attr( $this->field['id'] ) . ']" data-max="' . esc_attr( $args['max'] ) . '" data-min="' . esc_attr( $args['min'] ) . '">';

				if ( ! empty( $this->value ) && is_array( $this->value ) ) {

					$num = 0;

					foreach ( $this->value as $key => $value ) {

						echo '<div class="pbsettings-repeater-item">';
						echo '<div class="pbsettings-repeater-content">';
						foreach ( $this->field['fields'] as $field ) {

							$field_unique = ( ! empty( $this->unique ) ) ? $this->unique . '[' . $this->field['id'] . '][' . $num . ']' : $this->field['id'] . '[' . $num . ']';
							$field_value  = ( isset( $field['id'] ) && isset( $this->value[ $key ][ $field['id'] ] ) ) ? $this->value[ $key ][ $field['id'] ] : '';

							PBSettings::field( $field, $field_value, $field_unique, 'field/repeater' );

						}
						echo '</div>';
						echo '<div class="pbsettings-repeater-helper">';
						echo '<div class="pbsettings-repeater-helper-inner">';

						if ( ! in_array( 'sort', $args['disable_actions'] ) ) {
							echo '<i class="pbsettings-repeater-sort fas fa-arrows-alt"></i>';
						}

						if ( ! in_array( 'clone', $args['disable_actions'] ) ) {
							echo '<i class="pbsettings-repeater-clone far fa-clone"></i>';
						}

						if ( ! in_array( 'remove', $args['disable_actions'] ) ) {
							echo '<i class="pbsettings-repeater-remove pbsettings-confirm fas fa-times" data-confirm="' . esc_html__( 'Are you sure to delete this item?' ) . '"></i>';
						}

						echo '</div>';
						echo '</div>';
						echo '</div>';

						$num ++;

					}

				}

				echo '</div>';

				echo '<div class="pbsettings-repeater-alert pbsettings-repeater-max">' . esc_html__( 'You cannot add more.' ) . '</div>';
				echo '<div class="pbsettings-repeater-alert pbsettings-repeater-min">' . esc_html__( 'You cannot remove more.' ) . '</div>';
				echo '<a href="#" class="button button-primary pbsettings-repeater-add">' . $args['button_title'] . '</a>';

				echo $this->field_after();

			}

		}

		public function enqueue() {

			if ( ! wp_script_is( 'jquery-ui-sortable' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}

		}

	}
}
