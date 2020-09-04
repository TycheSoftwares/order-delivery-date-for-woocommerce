<?php
/**
 * Define the global arrays
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Configuration
 * @since       1.5
 */

global $orddd_lite_calendar_themes, $orddd_lite_weekdays, $orddd_lite_calendar_languages, $orddd_lite_date_formats, $orddd_lite_number_of_months, $orddd_lite_days, $orddd_lite_languages, $orddd_lite_languages_locale;

/**
 * Define the Language locales
 *
 * @since 1.9
 */
$orddd_lite_languages_locale = array(
	'Afrikaans'           => array( 'af_ZA.utf8', 'afr' ),
	'Arabic'              => array( 'ar_SA.utf8', 'ara', 'ar-SA' ),
	'Algerian Arabic'     => array( 'ar_DZ.utf8', 'ara', 'ar-DZ' ),
	'Azerbaijani'         => array( 'az_AZ.utf8', 'aze' ),
	'Indonesian'          => array( 'id_ID.utf8', 'ind', 'id-ID' ),
	'Malaysian'           => array( 'ms_MY.utf8', 'msa', 'ms-MY' ),
	'Dutch Belgian'       => array( 'nl_BE.utf8', 'nld', 'nl-BE', 'nl_NL.utf8' ),
	'Bosnian'             => array( 'bs_BA.utf8', 'bos' ),
	'Bulgarian'           => array( 'bg_BG.utf8', 'bul', 'bg-BG' ),
	'Catalan'             => array( 'ca_ES.utf8', 'cat', 'ca-ES' ),
	'Czech'               => array( 'cs_CZ.utf8', 'ces', 'cs-CZ' ),
	'Welsh'               => array( 'cy_GB.utf8', 'cym' ),
	'Danish'              => array( 'da_DK.UTF8', 'dan', 'da-DK' ),
	'German'              => array( 'de_DE.utf8', 'deu', 'de-DE' ),
	'Estonian'            => array( 'et_EE.utf8', 'est', 'et-EE' ),
	'Greek'               => array( 'el_GR.utf8', 'ell', 'el-GR' ),
	'English Australia'   => array( 'en-AU.utf8', 'eng' ),
	'English New Zealand' => array( 'en_NZ.utf8', 'eng', 'en-NZ' ),
	'English UK'          => array( 'en_GB.utf8', 'eng', 'en-GB' ),
	'Spanish'             => array( 'es_ES.utf8', 'spa', 'es-ES' ),
	'Esperanto'           => array( 'eo_EO.utf8', 'epo' ),
	'Basque'              => array( 'eu_ES.utf8', 'eus' ),
	'Faroese'             => array( 'fo_FO.utf8', 'fao' ),
	'French'              => array( 'fr_FR.utf8', 'fra', 'fr-FR' ),
	'French Swiss'        => array( 'fr_CH.utf8', 'fra', 'fr-Ch' ),
	'Galician'            => array( 'gl_ES.utf8', 'glg' ),
	'Albanian'            => array( 'sq_AL.utf8', 'sqi', 'sq-AL' ),
	'Korean'              => array( 'ko_KR.utf8', 'kor', 'ko-KR' ),
	'Hindi India'         => array( 'hi_IN.utf8', 'hin', 'hi-IN' ),
	'Hebrew'              => array( 'he_IL.utf8', 'heb', 'he_IL' ),
	'Croatian'            => array( 'hr_HR.utf8', 'hrv', 'hr-HR' ),
	'Armenian'            => array( 'hy_AM.utf8', 'hye' ),
	'Icelandic'           => array( 'is_IS.utf8', 'isl', 'is-IS' ),
	'Italian'             => array( 'it_IT.utf8', 'ita', 'it-IT' ),
	'Georgian'            => array( 'ka_GE.utf8', 'kat' ),
	'Khmer'               => array( 'km_KH.utf8', 'khm' ),
	'Latvian'             => array( 'lv_LV.utf8', 'lav' ),
	'Lithuanian'          => array( 'lt_LT.utf8', 'lit', 'lt-LT' ),
	'Macedonian'          => array( 'mk_MK.utf8', 'mkd', 'mk-MK' ),
	'Hungarian'           => array( 'hu_HU.utf8', 'hun', 'hu-HU' ),
	'Malayam'             => array( 'ml_IN.utf8', 'mal' ),
	'Dutch'               => array( 'nl_NL.utf8', 'nld', 'nl-NL' ),
	'Japanese'            => array( 'ja_JP.utf8', 'jpn', 'ja-JP' ),
	'Norwegian'           => array( 'no_NO.utf8', 'nob' ),
	'Thai'                => array( 'th_TH.utf8', 'tha', 'th-TH' ),
	'Persian'             => array( 'fa_IR.utf8', 'fa' ),
	'Polish'              => array( 'pl_PL.utf8', 'pol', 'pl-PL' ),
	'Portuguese'          => array( 'pt_PT.utf8', 'por', 'pt-PT' ),
	'Portuguese Brazil'   => array( 'pt_BR.utf8', 'por', 'pt-BR' ),
	'Romanian'            => array( 'ro_RO.utf8', 'ron', 'ro-RO' ),
	'Romansh'             => array( 'rm_RM.utf8', 'roh' ),
	'Russian'             => array( 'ru_RU.utf8', 'rus', 'ru-RU' ),
	'Slovak'              => array( 'sk_SK.utf8', 'slk', 'sk-SK' ),
	'Slovenian'           => array( 'sl_SI.utf8', 'slv', 'sl-SI' ),
	'Serbian'             => array( 'sr_CS.utf8' ),
	'Finnish'             => array( 'fi_FI.utf8', 'fin', 'fi-FI' ),
	'Swedish'             => array( 'sv_SE.utf8', 'swe', 'sv-SE' ),
	'Tamil'               => array( 'ta_IN.utf8', 'tam' ),
	'Vietnamese'          => array( 'vi_VN.utf8', 'vie', 'vi-VN' ),
	'Turkish'             => array( 'tr_TR.utf8', 'tur', 'tr-TR' ),
	'Ukrainian'           => array( 'uk_UA.utf8', 'ukr', 'uk-UA' ),
	'Chinese Hong Kong'   => array( 'zh_HK.utf8', 'zho' ),
	'Chinese Simplified'  => array( 'zh_CN.utf8', 'zho' ),
	'Chinese Traditional' => array( 'zh_TW.utf8', 'zho' ),
);

/**
 * Define the Date Formats available for the Delivery Date
 *
 * @since 1.9
 */
$orddd_lite_date_formats = array(
	'mm/dd/y'      => 'm/d/y',
	'dd/mm/y'      => 'd/m/y',
	'y/mm/dd'      => 'y/m/d',
	'dd.mm.y'      => 'd.m.y',
	'y.mm.dd'      => 'y.m.d',
	'yy-mm-dd'     => 'Y-m-d',
	'dd-mm-y'      => 'd-m-y',
	'd M, y'       => 'j M, y',
	'd M, yy'      => 'j M, Y',
	'd MM, y'      => 'j F, y',
	'd MM, yy'     => 'j F, Y',
	'DD, d MM, yy' => 'l, j F, Y',
	'D, M d, yy'   => 'D, M j, Y',
	'DD, M d, yy'  => 'l, M j, Y',
	'DD, MM d, yy' => 'l, F j, Y',
	'D, MM d, yy'  => 'D, F j, Y',
);

$orddd_lite_time_formats = array(
	1 => '12 hour',
	2 => '24 hour',
);

/**
 * Define the Number of months to be displayed in The Delivery Calendar
 *
 * @since 1.9
 */
$orddd_lite_number_of_months = array(
	1 => 1,
	2 => 2,
);

/**
 * Define the Calendar themes
 *
 * @since 1.9
 */
$orddd_lite_calendar_themes = array(
	'smoothness'     => 'Smoothness',
	'ui-lightness'   => 'UI lightness',
	'ui-darkness'    => 'UI darkness',
	'start'          => 'Start',
	'redmond'        => 'Redmond',
	'sunny'          => 'Sunny',
	'overcast'       => 'Overcast',
	'le-frog'        => 'Le Frog',
	'flick'          => 'Flick',
	'pepper-grinder' => 'Pepper Grinder',
	'eggplant'       => 'Eggplant',
	'dark-hive'      => 'Dark Hive',
	'cupertino'      => 'Cupertino',
	'south-street'   => 'South Street',
	'blitzer'        => 'Blitzer',
	'humanity'       => 'Humanity',
	'hot-sneaks'     => 'Hot sneaks',
	'excite-bike'    => 'Excite Bike',
	'vader'          => 'Vader',
	'dot-luv'        => 'Dot Luv',
	'mint-choc'      => 'Mint Choc',
	'black-tie'      => 'Black Tie',
	'trontastic'     => 'Trontastic',
	'swanky-purse'   => 'Swanky Purse',
);

/**
 * Define the Weekdays available for delivery
 *
 * @since 1.9
 */
$orddd_lite_weekdays = array(
	'orddd_lite_weekday_0' => __( 'Sunday', 'order-delivery-date' ),
	'orddd_lite_weekday_1' => __( 'Monday', 'order-delivery-date' ),
	'orddd_lite_weekday_2' => __( 'Tuesday', 'order-delivery-date' ),
	'orddd_lite_weekday_3' => __( 'Wednesday', 'order-delivery-date' ),
	'orddd_lite_weekday_4' => __( 'Thursday', 'order-delivery-date' ),
	'orddd_lite_weekday_5' => __( 'Friday', 'order-delivery-date' ),
	'orddd_lite_weekday_6' => __( 'Saturday', 'order-delivery-date' ),
);

/**
 * Define the weekdays available to select the first day of the week.
 *
 * @since 1.9
 */
$orddd_lite_days = array(
	'0' => 'Sunday',
	'1' => 'Monday',
	'2' => 'Tuesday',
	'3' => 'Wednesday',
	'4' => 'Thursday',
	'5' => 'Friday',
	'6' => 'Saturday',
);

/**
 * Define the Languages
 *
 * @since 1.9
 */
$orddd_lite_languages = array(
	'af'    => 'Afrikaans',
	'ar'    => 'Arabic',
	'ar-DZ' => 'Algerian Arabic',
	'az'    => 'Azerbaijani',
	'id'    => 'Indonesian',
	'ms'    => 'Malaysian',
	'nl-BE' => 'Dutch Belgian',
	'bs'    => 'Bosnian',
	'bg'    => 'Bulgarian',
	'ca'    => 'Catalan',
	'cs'    => 'Czech',
	'cy-GB' => 'Welsh',
	'da'    => 'Danish',
	'de'    => 'German',
	'et'    => 'Estonian',
	'el'    => 'Greek',
	'en-AU' => 'English Australia',
	'en-NZ' => 'English New Zealand',
	'en-GB' => 'English UK',
	'es'    => 'Spanish',
	'eo'    => 'Esperanto',
	'eu'    => 'Basque',
	'fa'    => 'Persian',
	'fo'    => 'Faroese',
	'fr'    => 'French',
	'fr-CH' => 'French Swiss',
	'gl'    => 'Galician',
	'sq'    => 'Albanian',
	'ko'    => 'Korean',
	'hi'    => 'Hindi India',
	'hr'    => 'Croatian',
	'hy'    => 'Armenian',
	'he'    => 'Hebrew',
	'is'    => 'Icelandic',
	'it'    => 'Italian',
	'ka'    => 'Georgian',
	'km'    => 'Khmer',
	'lv'    => 'Latvian',
	'lt'    => 'Lithuanian',
	'mk'    => 'Macedonian',
	'hu'    => 'Hungarian',
	'ml'    => 'Malayam',
	'nl'    => 'Dutch',
	'ja'    => 'Japanese',
	'no'    => 'Norwegian',
	'th'    => 'Thai',
	'pl'    => 'Polish',
	'pt'    => 'Portuguese',
	'pt-BR' => 'Portuguese Brazil',
	'ro'    => 'Romanian',
	'rm'    => 'Romansh',
	'ru'    => 'Russian',
	'sk'    => 'Slovak',
	'sl'    => 'Slovenian',
	'sr'    => 'Serbian',
	'fi'    => 'Finnish',
	'sv'    => 'Swedish',
	'ta'    => 'Tamil',
	'vi'    => 'Vietnamese',
	'tr'    => 'Turkish',
	'uk'    => 'Ukrainian',
	'zh-HK' => 'Chinese Hong Kong',
	'zh-CN' => 'Chinese Simplified',
	'zh-TW' => 'Chinese Traditional',
);

/**
 * Define the constants
 *
 * @since 1.9
 */
define( 'ORDDD_LITE_DELIVERY_DATE_FIELD_LABEL', 'Delivery Date' );
define( 'ORDDD_LITE_DELIVERY_TIME_FIELD_LABEL', 'Time Slot' );
define( 'ORDDD_LITE_DELIVERY_DATE_FIELD_PLACEHOLDER', 'Choose a Date' );
define( 'ORDDD_LITE_DELIVERY_DATE_FIELD_NOTE', 'We will try our best to deliver your order on the specified date.' );
define( 'ORDDD_LITE_DELIVERY_DATE_FORMAT', 'd MM, yy' );
define( 'ORDDD_LITE_LOCKOUT_DATE_FORMAT', 'n-j-Y' );
define( 'ORDDD_LITE_HOLIDAY_DATE_FORMAT', 'n-j-Y' );
define( 'ORDDD_LITE_CALENDAR_THEME', 'smoothness' );
define( 'ORDDD_LITE_CALENDAR_THEME_NAME', 'Smoothness' );

/**
 * Add some constants for hooks
 *
 * @since 1.9
 */
if ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) === 'billing_section' ) {
	define( 'ORDDD_LITE_SHOPPING_CART_HOOK', 'woocommerce_after_checkout_billing_form' );
} elseif ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) === 'shipping_section' ) {
	define( 'ORDDD_LITE_SHOPPING_CART_HOOK', 'woocommerce_after_checkout_shipping_form' );
} elseif ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) === 'before_order_notes' ) {
	define( 'ORDDD_LITE_SHOPPING_CART_HOOK', 'woocommerce_before_order_notes' );
} elseif ( get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) === 'after_order_notes' ) {
	define( 'ORDDD_LITE_SHOPPING_CART_HOOK', 'woocommerce_after_order_notes' );
} elseif ( 'after_your_order_table' === get_option( 'orddd_lite_delivery_date_fields_on_checkout_page' ) ) {
	define( 'ORDDD_LITE_SHOPPING_CART_HOOK', 'woocommerce_review_order_before_payment' );
} elseif ( get_option( 'orddd_lite_date_in_shipping' ) === 'on' ) {
	define( 'ORDDD_LITE_SHOPPING_CART_HOOK', 'woocommerce_after_checkout_shipping_form' );
} else {
	define( 'ORDDD_LITE_SHOPPING_CART_HOOK', 'woocommerce_after_checkout_billing_form' );
}
