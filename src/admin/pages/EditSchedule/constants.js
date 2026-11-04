// ── Constants ─────────────────────────────────────────────────────────────────

import { __ } from '@wordpress/i18n';

/**
 * JS equivalent of orddd_get_delivery_pickup_labels().
 * Returns the label set for the given schedule type ('delivery' | 'pickup').
 */
export const getScheduleLabels = ( type ) => {
	const map = {
		delivery: {
			enable:                    () => __( 'Enable Delivery Date:',                                                          'order-delivery-date' ),
			checkout_options:          () => __( 'Delivery Checkout options:',                                                     'order-delivery-date' ),
			estimated_date:            () => __( 'Estimated Delivery Date',                                                        'order-delivery-date' ),
			mdt:                       () => __( 'Minimum Delivery Time (hours):',                                                 'order-delivery-date' ),
			range:                     () => __( 'Delivery Range (days):',                                                         'order-delivery-date' ),
			weekdays_dates:            () => __( 'Delivery Days:',                                       'order-delivery-date' ),
			delivery_days_table:       () => __( 'Delivery Days/Dates',                                                            'order-delivery-date' ),
			specific_dates:            () => __( 'Enable Specific Delivery Dates:',                                                'order-delivery-date' ),
			add_specific_dates:        () => __( 'Add Specific Delivery Dates',                                                    'order-delivery-date' ),
			time_slot_dates:           () => __( 'Select Delivery Dates:',                                                         'order-delivery-date' ),
			specific_dates_charges:    () => __( 'Additional Charges for Specific Delivery Dates:',                                'order-delivery-date' ),
			delivery_schedule:         () => __( 'Delivery Schedule',                                                              'order-delivery-date' ),
			same_day_cutoff:           () => __( 'Cut-off time for Same Day Delivery Orders:',                                     'order-delivery-date' ),
			next_day_cutoff:           () => __( 'Cut-off time for Next Day Delivery Orders:',                                     'order-delivery-date' ),
			same_day_charges:          () => __( 'Additional Charges for Same Day Delivery:',                                      'order-delivery-date' ),
			next_day_charges:          () => __( 'Additional Charges for Next Day Delivery:',                                      'order-delivery-date' ),
			waive_charges:             () => __( 'Minimum Order Amount for Waiving off Delivery Charges:',                         'order-delivery-date' ),
			rush_hour_charges:         () => __( 'Enable Delivery Charges Based on Number of Days (Rush Delivery Charges):',       'order-delivery-date' ),
		},
		pickup: {
			enable:                    () => __( 'Enable Pickup Date:',                                                            'order-delivery-date' ),
			checkout_options:          () => __( 'Pickup Checkout options:',                                                       'order-delivery-date' ),
			estimated_date:            () => __( 'Estimated Pickup Date',                                                          'order-delivery-date' ),
			mdt:                       () => __( 'Minimum Pickup Time (hours):',                                                   'order-delivery-date' ),
			range:                     () => __( 'Pickup Range (days):',                                                           'order-delivery-date' ),
			weekdays_dates:            () => __( 'Pickup Days & Specific Dates:',                                                  'order-delivery-date' ),
			delivery_days_table:       () => __( 'Pickup Days/Dates',                                                              'order-delivery-date' ),
			specific_dates:            () => __( 'Enable Specific Pickup Dates:',                                                  'order-delivery-date' ),
			add_specific_dates:        () => __( 'Add Specific Pickup Dates',                                                      'order-delivery-date' ),
			time_slot_dates:           () => __( 'Select Pickup Dates:',                                                           'order-delivery-date' ),
			specific_dates_charges:    () => __( 'Additional Charges for Specific Pickup Dates:',                                  'order-delivery-date' ),
			delivery_schedule:         () => __( 'Pickup Schedule',                                                                'order-delivery-date' ),
			same_day_cutoff:           () => __( 'Cut-off time for Same Day Pickup Orders:',                                       'order-delivery-date' ),
			next_day_cutoff:           () => __( 'Cut-off time for Next Day Pickup Orders:',                                       'order-delivery-date' ),
			same_day_charges:          () => __( 'Additional Charges for Same Day Pickup:',                                        'order-delivery-date' ),
			next_day_charges:          () => __( 'Additional Charges for Next Day Pickup:',                                        'order-delivery-date' ),
			waive_charges:             () => __( 'Minimum Order Amount for Waiving off Pickup Charges:',                           'order-delivery-date' ),
			rush_hour_charges:         () => __( 'Enable Pickup Charges Based on Number of Days (Rush Pickup Charges):',           'order-delivery-date' ),
		},
	};
	return map[ type ] || map.delivery;
};

export const WEEK_DAYS = [
	{ key: 'orddd_lite_weekday_0', label: __( 'Sunday',    'order-delivery-date' ) },
	{ key: 'orddd_lite_weekday_1', label: __( 'Monday',    'order-delivery-date' ) },
	{ key: 'orddd_lite_weekday_2', label: __( 'Tuesday',   'order-delivery-date' ) },
	{ key: 'orddd_lite_weekday_3', label: __( 'Wednesday', 'order-delivery-date' ) },
	{ key: 'orddd_lite_weekday_4', label: __( 'Thursday',  'order-delivery-date' ) },
	{ key: 'orddd_lite_weekday_5', label: __( 'Friday',    'order-delivery-date' ) },
	{ key: 'orddd_lite_weekday_6', label: __( 'Saturday',  'order-delivery-date' ) },
];

export const DEFAULT_DELIVERY_DAYS = WEEK_DAYS.map( ( d, i ) => ( {
	key:                d.key,
	name:               d.label,
	enable:             'checked',
	additional_charges: '',
	charges_type:       'fixed',
	charges_label:      __( 'Delivery Charges', 'order-delivery-date' ),
	max_orders:         '',
	edit:               false,
} ) );

export const BASED_ON_OPTIONS = [
	{ value: 'shipping_methods',   label: __( 'Shipping Method',    'order-delivery-date' ) },
	{ value: 'product_categories', label: __( 'Product Categories', 'order-delivery-date' ) },
	{ value: 'product_tags',       label: __( 'Product Tags',       'order-delivery-date' ) },
	{ value: 'products',           label: __( 'Products',           'order-delivery-date' ) },
	{ value: 'order_type',         label: __( 'Order Type',         'order-delivery-date' ) },
	{ value: 'user_role',          label: __( 'User Role',          'order-delivery-date' ) },
	{ value: 'orddd_locations',    label: __( 'Pickup Locations',   'order-delivery-date' ) },
];

export const CHARGES_TYPE_OPTIONS = [
	{ value: 'fixed',      label: __( 'Fixed',      'order-delivery-date' ) },
	{ value: 'percentage', label: __( 'Percentage', 'order-delivery-date' ) },
];

// ── Specific dates form ───────────────────────────────────────────────────────

export const SPEC_DATE_RANGE_OPTIONS = [
	{ value: 'individual', label: __( 'Individual', 'order-delivery-date' ) },
	{ value: 'date_range', label: __( 'Date Range', 'order-delivery-date' ) },
];

export const SPEC_DATE_EMPTY = {
	range_type:         'individual',
	delivery_dates:     [],
	date_from:          '',
	date_to:            '',
	additional_charges: '',
	charges_type:       'fixed',
	charges_label:      '',
	max_orders:         '',
};

// ── Time slot form ────────────────────────────────────────────────────────────

export const TIME_SLOT_FOR_OPTIONS = [
	{ value: 'weekdays',       label: __( 'Weekdays',       'order-delivery-date' ) },
	{ value: 'specific_dates', label: __( 'Specific Dates', 'order-delivery-date' ) },
];

export const WEEKDAY_OPTIONS = [
	{ value: '', label: __( 'Select Weekday', 'order-delivery-date' ) },
	...WEEK_DAYS.map( ( d ) => ( { value: d.key, label: d.label } ) ),
];

export const INDIVIDUAL_SLOT_EMPTY = {
	time_slot_for: 'weekdays',
	delivery_days: [],
	timeRanges:    [ { from_time: '', to_time: '' } ],
	max_orders:    '',
	charges:       '',
	charges_type:  'fixed',
	charges_label: '',
};

export const BULK_SLOT_EMPTY = {
	time_slot_for: 'weekdays',
	delivery_days: [],
	duration:      '',
	interval:      '',
	from_time:     '',
	to_time:       '',
	max_orders:    '',
	charges:       '',
	charges_type:  'fixed',
	charges_label: '',
};

// ── Main form default ─────────────────────────────────────────────────────────

export const EMPTY_FORM = {
	schedule_type:                              'delivery',
	delivery_settings_based_on:                'shipping_methods',
	shipping_methods:                           [],
	product_categories:                         [],
	shipping_methods_for_categories:            [],
	product_tags:                               [],
	shipping_methods_for_tags:                  [],
	products:                                   [],
	shipping_methods_for_products:              [],
	orddd_locations:                            [],
	categories_for_pickup_locations:            [],
	order_type:                                 [],
	user_role:                                  [],
	enable_shipping_based_delivery:             'on',
	orddd_shipping_settings_status:             'on',
	orddd_delivery_checkout_options:            'delivery_calendar',
	delivery_type:                              { weekdays: 'on', specific_dates: '' },
	date_lockout:                               '',
	date_mandatory_field:                       '',
	numder_of_dates:                            '30',
	orddd_shipping_based_delivery_date_field_label:     '',
	orddd_shipping_based_delivery_date_field_note_text: '',
	minimum_delivery_time:                      '',
	orddd_min_between_days:                     '',
	orddd_max_between_days:                     '',
	enable_time_slot:                           '',
	timeslot_mandatory_field:                   '',
	timeslot_asap_option:                       '',
	orddd_asap_charges:                         '',
	orddd_asap_charges_type:                    'fixed',
	orddd_asap_charges_label:                   '',
	orddd_shipping_based_delivery_timeslot_field_label: '',
	time_settings:                              { enable: '', from_hours: '', to_hours: '' },
	orddd_priority_for_delivery_schedule:       '',
	same_day:                                   { after_hours: '', after_minutes: '00' },
	next_day:                                   { after_hours: '', after_minutes: '00' },
	same_day_charges:                           '',
	orddd_same_day_charges_type:                'fixed',
	next_day_charges:                           '',
	next_day_charges_type:                      'fixed',
	max_order_total:                            '',
	orddd_delivery_charges_based_on_days_enable: '',
	weekday_based_schedule_enable:              '',
};
