<?php
/**
 * Order Delivery Date Appearance Settings
 *
 * @author Tyche Softwares
 * @package Order-Delivery-Date-Pro-for-WooCommerce/Admin/Settings/General
 * @since 3.9
 * @category Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Orddd_Lite_Appearance_Settings Class
 *
 * @class Orddd_Lite_Appearance_Settings
 */
class Orddd_Lite_Appearance_Settings {

	/**
	 * Callback for adding Appearance tab settings
	 *
	 * @since 1.5
	 */
	public static function orddd_lite_appearance_admin_setting_callback() { }

	/**
	 * Callback for adding Calendar Language setting
	 *
	 * @param array $args Callback arguments.
	 * @since 1.5
	 */
	public static function orddd_lite_appearance_calendar_language_callback( $args ) {
		global $orddd_lite_languages;
		$language_selected = get_option( 'orddd_lite_language_selected' );
		if ( '' === $language_selected ) {
			$language_selected = 'en-GB';
		}

		?>		
		<select id="orddd_lite_language_selected" name="orddd_lite_language_selected">
		<?php

		foreach ( $orddd_lite_languages as $key => $value ) {
			$sel = '';
			if ( $key === $language_selected ) {
				$sel = 'selected';
			}
			?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $sel ); ?>><?php echo esc_attr( $value ); ?></option>
			<?php
		}
		?>
		</select>
		<label for="orddd_lite_language_selected"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Date formats setting
	 *
	 * @param array $args Callback arguments.
	 * @since 1.5
	 */
	public static function orddd_lite_appearance_date_formats_callback( $args ) {
		global $orddd_lite_date_formats;

		?>
		<select name="orddd_lite_delivery_date_format" id="orddd_lite_delivery_date_format" size="1">
		<?php
		foreach ( $orddd_lite_date_formats as $k => $format ) {
			printf(
				"<option %s value='%s'>%s</option>\n",
				selected( $k, get_option( 'orddd_lite_delivery_date_format' ), false ),
				esc_attr( $k ),
				esc_attr( gmdate( $format ) )
			);
		}

		?>
		</select>
		<label for="orddd_lite_delivery_date_format"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Time format for time sliders setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since
	 */
	public static function orddd_lite_time_format_callback( $args ) {
		global $orddd_lite_time_formats;
		echo '<select name="orddd_lite_delivery_time_format" id="orddd_lite_delivery_time_format" size="1">';

		foreach ( $orddd_lite_time_formats as $k => $format ) {
			printf(
				"<option %s value='%s'>%s</option>\n",
				selected( $k, get_option( 'orddd_lite_delivery_time_format' ), false ),
				esc_attr( $k ),
				esc_attr( $format )
			);
		}

		echo '</select>';

		?>
		<label for="orddd_lite_delivery_time_format"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding First day of week setting
	 *
	 * @param array $args Callback arguments.
	 * @since 1.5
	 */
	public static function orddd_lite_appearance_first_day_of_week_callback( $args ) {
		global $orddd_lite_days;
		$day_selected = get_option( 'orddd_lite_start_of_week' );
		if ( '' === $day_selected ||
		false === $day_selected ) {
			$day_selected = 0;
		}

		?>
		<select id="orddd_lite_start_of_week" name="orddd_lite_start_of_week">
		<?php

		foreach ( $orddd_lite_days as $key => $value ) {
			$sel = '';
			if ( (int) $key === (int) $day_selected ) {
				$sel = ' selected ';
			}
			printf(
				"<option value='%s' %s>%s</option>",
				esc_attr( $key ),
				esc_attr( $sel ),
				esc_attr( $value )
			);
		}
		?>
		</select>
		<label for="orddd_lite_start_of_week"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Date field label setting
	 *
	 * @param array $args Callback arguments.
	 * @since 1.5
	 */
	public static function orddd_lite_delivery_date_field_label_callback( $args ) {
		?>
		<input type="text" name="orddd_lite_delivery_date_field_label" id="orddd_lite_delivery_date_field_label" value="<?php echo esc_attr( get_option( 'orddd_lite_delivery_date_field_label' ) ); ?>" maxlength="40"/>
		<label for="orddd_lite_delivery_date_field_label"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Time slot field label setting
	 *
	 * @param array $args Extra arguments containing label & class for the field.
	 * @since 3.11.0
	 */
	public static function orddd_lite_delivery_timeslot_field_label_callback( $args ) {
		?>
		<input type="text" name="orddd_lite_delivery_timeslot_field_label" id="orddd_lite_delivery_timeslot_field_label" value="<?php echo esc_attr( get_option( 'orddd_lite_delivery_timeslot_field_label' ) ); ?>" maxlength="40"/>
		<label for="orddd_lite_delivery_timeslot_field_label"><?php echo esc_attr( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Date field placeholder setting
	 *
	 * @param array $args Callback arguments.
	 * @since 1.5
	 */
	public static function orddd_lite_delivery_date_field_placeholder_callback( $args ) {
		?>
		<input type="text" name="orddd_lite_delivery_date_field_placeholder" id="orddd_lite_delivery_date_field_placeholder" value="<?php echo esc_attr( get_option( 'orddd_lite_delivery_date_field_placeholder' ) ); ?>" maxlength="40"/>
		<label for="orddd_lite_delivery_date_field_placeholder"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Date field note text setting
	 *
	 * @param array $args Callback arguments.
	 * @since 1.5
	 */
	public static function orddd_lite_delivery_date_field_note_text_callback( $args ) {
		?>
		<textarea rows="2" cols="90" name="orddd_lite_delivery_date_field_note" id="orddd_lite_delivery_date_field_note"><?php echo esc_attr( stripslashes( get_option( 'orddd_lite_delivery_date_field_note' ) ) ); ?></textarea>
		<label for="orddd_lite_delivery_date_field_note"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Number of months setting
	 *
	 * @param array $args Callback arguments.
	 * @since 1.5
	 */
	public static function orddd_lite_appearance_number_of_months_callback( $args ) {
		global $orddd_lite_number_of_months;
		?>
		<select name="orddd_lite_number_of_months" id="orddd_lite_number_of_months" size="1">
		<?php
		foreach ( $orddd_lite_number_of_months as $k => $v ) {
			printf(
				"<option %s value='%s'>%s</option>\n",
				selected( $k, get_option( 'orddd_lite_number_of_months' ), false ),
				esc_attr( $k ),
				esc_attr( $v )
			);
		}
		?>
		</select>
		<label for="orddd_lite_number_of_months"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}

	/**
	 * Callback for adding Delivery Date fields in Shipping section setting
	 *
	 * @param array $args Callback arguments.
	 * @since 1.5
	 */
	public static function orddd_lite_delivery_date_in_shipping_section_callback( $args ) {
		$orddd_lite_date_in_billing             = 'checked';
		$orddd_lite_date_in_shipping            = '';
		$orddd_lite_date_before_order_notes     = '';
		$orddd_lite_date_after_order_notes      = '';
		$orddd_lite_date_after_your_order_table = '';
		if ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) === 'billing_section' ) {
			$orddd_lite_date_in_billing         = 'checked';
			$orddd_lite_date_in_shipping        = '';
			$orddd_lite_date_before_order_notes = '';
			$orddd_lite_date_after_order_notes  = '';
		} elseif ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) === 'shipping_section' ) {
			$orddd_lite_date_in_shipping        = 'checked';
			$orddd_lite_date_in_billing         = '';
			$orddd_lite_date_before_order_notes = '';
			$orddd_lite_date_after_order_notes  = '';
		} elseif ( 'before_order_notes' === get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) ) {
			$orddd_lite_date_before_order_notes = 'checked';
			$orddd_lite_date_in_billing         = '';
			$orddd_lite_date_in_shipping        = '';
			$orddd_lite_date_after_order_notes  = '';
		} elseif ( 'after_order_notes' === get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) ) {
			$orddd_lite_date_after_order_notes  = 'checked';
			$orddd_lite_date_in_billing         = '';
			$orddd_lite_date_in_shipping        = '';
			$orddd_lite_date_before_order_notes = '';
		} elseif ( 'after_your_order_table' === get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) ) {
			$orddd_lite_date_after_your_order_table = 'checked';
			$orddd_lite_date_in_billing             = '';
			$orddd_lite_date_in_shipping            = '';
			$orddd_lite_date_before_order_notes     = '';
			$orddd_lite_date_after_order_notes      = '';
		}

		?>
		<input type="radio" name="orddd_lite_delivery_date_fields_on_checkout_page" id="orddd_lite_delivery_date_fields_on_checkout_page" value="billing_section" <?php echo esc_attr( $orddd_lite_date_in_billing ); ?> > <?php esc_attr_e( 'In Billing Section', 'order-delivery-date' ); ?> <br>
		<input type="radio" name="orddd_lite_delivery_date_fields_on_checkout_page" id="orddd_lite_delivery_date_fields_on_checkout_page" value="shipping_section" <?php echo esc_attr( $orddd_lite_date_in_shipping ); ?> > <?php esc_attr_e( 'In Shipping Section', 'order-delivery-date' ); ?> <br>
		<input type="radio" name="orddd_lite_delivery_date_fields_on_checkout_page" id="orddd_lite_delivery_date_fields_on_checkout_page" value="before_order_notes" <?php echo esc_attr( $orddd_lite_date_before_order_notes ); ?> > <?php esc_attr_e( 'Before Order Notes', 'order-delivery-date' ); ?> <br>
		<input type="radio" name="orddd_lite_delivery_date_fields_on_checkout_page" id="orddd_lite_delivery_date_fields_on_checkout_page" value="after_order_notes" <?php echo esc_attr( $orddd_lite_date_after_order_notes ); ?> > <?php esc_attr_e( 'After Order Notes', 'order-delivery-date' ); ?> <br>
		<input type="radio" name="orddd_lite_delivery_date_fields_on_checkout_page" id="orddd_lite_delivery_date_fields_on_checkout_page" value="after_your_order_table" <?php echo esc_attr( $orddd_lite_date_after_your_order_table ); ?> > <?php esc_attr_e( 'Between Your Order & Payment Section', 'order-delivery-date' ); ?><br>
		<label for="orddd_lite_delivery_date_fields_on_checkout_page"><?php echo wp_kses_post( $args[0] ); ?> </label>
		<?php
	}

	/**
	 * Callback for adding Delivery Date field on Cart page setting
	 *
	 * @param array $args Callback arguments.
	 * @since 1.5
	 */
	public static function orddd_lite_delivery_date_on_cart_page_callback( $args ) {
		$delivery_date_on_cart_page = '';
		if ( get_option( ' orddd_lite_delivery_date_on_cart_page' ) === 'on' ) {
			$delivery_date_on_cart_page = 'checked';
		}

		?>
		<input type="checkbox" name="orddd_lite_delivery_date_on_cart_page" id="orddd_lite_delivery_date_on_cart_page" class="day-checkbox" <?php echo esc_attr( $delivery_date_on_cart_page ); ?> />
		<label for="orddd_lite_delivery_date_on_cart_page"><?php echo wp_kses_post( $args[0] ); ?> </label>
		<?php
	}

	/**
	 * Callback for adding Calendar theme setting
	 *
	 * @param array $args Callback arguments.
	 * @since 1.5
	 */
	public static function orddd_lite_appearance_calendar_theme_callback( $args ) {
		global $orddd_lite_calendar_themes;
		$language_selected = get_option( 'orddd_lite_language_selected' );
		if ( '' === $language_selected ||
			false === $language_selected ) {
			$language_selected = 'en-GB';
		}

		$first_day_of_week = get_option( 'orddd_lite_start_of_week' );
		if ( '' === $first_day_of_week ||
			false === $first_day_of_week ) {
			$first_day_of_week = 1;
		}
		?>
			<input type="hidden" name="orddd_lite_calendar_theme" id="orddd_lite_calendar_theme" value="<?php echo esc_attr( get_option( 'orddd_lite_calendar_theme' ) ); ?>">
			<input type="hidden" name="orddd_lite_calendar_theme_name" id="orddd_lite_calendar_theme_name" value="<?php echo esc_attr( get_option( 'orddd_lite_calendar_theme_name' ) ); ?>">
		<?php
			echo '<script>
				jQuery( document ).ready( function( ) {
					var calendar_themes = ' . wp_json_encode( $orddd_lite_calendar_themes ) . '
					jQuery( "#switcher" ).themeswitcher( {
						onclose: function( ) {
							var cookie_name = this.cookiename;
							jQuery( "input#orddd_lite_calendar_theme" ).val( jQuery.cookie( cookie_name ) );
							jQuery.each( calendar_themes, function( key, value ) {
								if( jQuery.cookie( cookie_name ) == key ) {
									jQuery( "input#orddd_lite_calendar_theme_name" ).val( value );
								}
							});
							jQuery( "<link/>", {
								rel: "stylesheet",
								type: "text/css",
								href: "' . esc_url( plugins_url() . '/order-delivery-date-for-woocommerce/css/datepicker.css' ) . '"
							}).appendTo("head");
						},
						imgpath: "' . esc_url( plugins_url() . '/order-delivery-date-for-woocommerce/images/' ) . '",
						loadTheme: "' . esc_attr( get_option( 'orddd_lite_calendar_theme_name' ) ) . '",
						
					});
				});

				jQuery( function() {
					jQuery.datepicker.setDefaults( jQuery.datepicker.regional[ "" ] );
					jQuery( "#datepicker" ).datepicker();
					jQuery( "#datepicker" ).datepicker( "option", "firstDay", ' . esc_attr( $first_day_of_week ) . ' );
					jQuery( "#datepicker" ).datepicker( "option", jQuery.datepicker.regional[ "' . esc_attr( $language_selected ) . '" ] );
					jQuery( "#orddd_lite_language_selected" ).change(function() {
						jQuery( "#datepicker" ).datepicker( "option", jQuery.datepicker.regional[ jQuery( this ).val() ] );
						});
					});
					
			</script>
			<div id="switcher"></div>
			<br><strong>' . esc_attr_e( 'Preview theme:', 'order-delivery-date' ) . '</strong><br>
			<div id="datepicker" style="width:300px"></div>';

		?>
		<label for="orddd_lite_calendar_theme_name"><?php echo wp_kses_post( $args[0] ); ?></label>
		<?php
	}
}
