/**
 * WordPress dependencies
 */
import { useState, useEffect, useRef } from '@wordpress/element';
import {
	ToggleControl,
	TextControl,
	SelectControl,
	Notice,
	Spinner,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

/**
 * External dependencies
 */
import { useForm, Controller, useWatch } from 'react-hook-form';

/**
 * Internal dependencies
 */
import SettingsPanel, { AccordionCard, SettingRow } from '../../../components/SettingsPanel';

// ── Option lists ──────────────────────────────────────────────────────────────

// Formats a Date using jQuery UI datepicker format tokens.
const DAY_NAMES_SHORT   = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ];
const DAY_NAMES_LONG    = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
const MONTH_NAMES_SHORT = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
const MONTH_NAMES_LONG  = [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ];

const formatJQueryUIDate = ( date, format ) => {
	const map = {
		DD: DAY_NAMES_LONG[ date.getDay() ],
		D:  DAY_NAMES_SHORT[ date.getDay() ],
		MM: MONTH_NAMES_LONG[ date.getMonth() ],
		M:  MONTH_NAMES_SHORT[ date.getMonth() ],
		dd: String( date.getDate() ).padStart( 2, '0' ),
		d:  String( date.getDate() ),
		mm: String( date.getMonth() + 1 ).padStart( 2, '0' ),
		m:  String( date.getMonth() + 1 ),
		yy: String( date.getFullYear() ),
		y:  String( date.getFullYear() ).slice( -2 ),
	};
	return format.replace( /DD|D|MM|M|dd|d|mm|m|yy|y/g, ( token ) => map[ token ] );
};

const TODAY = new Date();

const DATE_FORMAT_OPTIONS = [
	'mm/dd/y',
	'dd/mm/y',
	'dd/mm/yy',
	'y/mm/dd',
	'mm/dd/y, D',
	'dd.mm.y',
	'y.mm.dd',
	'yy-mm-dd',
	'dd-mm-y',
	'd M, y',
	'd M, yy',
	'd MM, y',
	'd MM, yy',
	'DD, d MM, yy',
	'D, M d, yy',
	'DD, M d, yy',
	'DD, MM d, yy',
	'D, MM d, yy',
	'DD d MM yy',
	'DD d MM',
].map( ( fmt ) => ( { value: fmt, label: `${ formatJQueryUIDate( TODAY, fmt ) }` } ) );

const TIME_FORMAT_OPTIONS = [
	{ value: '1', label: __( '12 hour', 'order-delivery-date' ) },
	{ value: '2', label: __( '24 hour', 'order-delivery-date' ) },
];

const START_OF_WEEK_OPTIONS = [
	{ value: '0', label: __( 'Sunday', 'order-delivery-date' )    },
	{ value: '1', label: __( 'Monday', 'order-delivery-date' )    },
	{ value: '2', label: __( 'Tuesday', 'order-delivery-date' )   },
	{ value: '3', label: __( 'Wednesday', 'order-delivery-date' ) },
	{ value: '4', label: __( 'Thursday', 'order-delivery-date' )  },
	{ value: '5', label: __( 'Friday', 'order-delivery-date' )    },
	{ value: '6', label: __( 'Saturday', 'order-delivery-date' )  },
];

const NUMBER_OF_MONTHS_OPTIONS = [
	{ value: '1', label: '1' },
	{ value: '2', label: '2' },
];

const CALENDAR_THEME_OPTIONS = [
	{ value: 'smoothness',     label: __( 'Smoothness', 'order-delivery-date' )     },
	{ value: 'ui-lightness',   label: __( 'UI Lightness', 'order-delivery-date' )   },
	{ value: 'ui-darkness',    label: __( 'UI Darkness', 'order-delivery-date' )    },
	{ value: 'start',          label: __( 'Start', 'order-delivery-date' )          },
	{ value: 'redmond',        label: __( 'Redmond', 'order-delivery-date' )        },
	{ value: 'sunny',          label: __( 'Sunny', 'order-delivery-date' )          },
	{ value: 'overcast',       label: __( 'Overcast', 'order-delivery-date' )       },
	{ value: 'le-frog',        label: __( 'Le Frog', 'order-delivery-date' )        },
	{ value: 'flick',          label: __( 'Flick', 'order-delivery-date' )          },
	{ value: 'pepper-grinder', label: __( 'Pepper Grinder', 'order-delivery-date' ) },
	{ value: 'eggplant',       label: __( 'Eggplant', 'order-delivery-date' )       },
	{ value: 'dark-hive',      label: __( 'Dark Hive', 'order-delivery-date' )      },
	{ value: 'cupertino',      label: __( 'Cupertino', 'order-delivery-date' )      },
	{ value: 'south-street',   label: __( 'South Street', 'order-delivery-date' )   },
	{ value: 'blitzer',        label: __( 'Blitzer', 'order-delivery-date' )        },
	{ value: 'humanity',       label: __( 'Humanity', 'order-delivery-date' )       },
	{ value: 'hot-sneaks',     label: __( 'Hot Sneaks', 'order-delivery-date' )     },
	{ value: 'excite-bike',    label: __( 'Excite Bike', 'order-delivery-date' )    },
	{ value: 'vader',          label: __( 'Vader', 'order-delivery-date' )          },
	{ value: 'dot-luv',        label: __( 'Dot Luv', 'order-delivery-date' )        },
	{ value: 'mint-choc',      label: __( 'Mint Choc', 'order-delivery-date' )      },
	{ value: 'black-tie',      label: __( 'Black Tie', 'order-delivery-date' )      },
	{ value: 'trontastic',     label: __( 'Trontastic', 'order-delivery-date' )     },
	{ value: 'swanky-purse',   label: __( 'Swanky Purse', 'order-delivery-date' )   },
	{ value: 'new-theme-1',    label: __( 'Custom Theme 1', 'order-delivery-date' ) },
	{ value: 'new-theme-2',    label: __( 'Custom Theme 2', 'order-delivery-date' ) },
];

const LANGUAGE_OPTIONS = [
	{ value: 'af',    label: __( 'Afrikaans', 'order-delivery-date' )            },
	{ value: 'ar',    label: __( 'Arabic', 'order-delivery-date' )               },
	{ value: 'ar-DZ', label: __( 'Algerian Arabic', 'order-delivery-date' )      },
	{ value: 'az',    label: __( 'Azerbaijani', 'order-delivery-date' )          },
	{ value: 'id',    label: __( 'Indonesian', 'order-delivery-date' )           },
	{ value: 'ms',    label: __( 'Malaysian', 'order-delivery-date' )            },
	{ value: 'nl-BE', label: __( 'Dutch Belgian', 'order-delivery-date' )        },
	{ value: 'bs',    label: __( 'Bosnian', 'order-delivery-date' )              },
	{ value: 'bg',    label: __( 'Bulgarian', 'order-delivery-date' )            },
	{ value: 'ca',    label: __( 'Catalan', 'order-delivery-date' )              },
	{ value: 'cs',    label: __( 'Czech', 'order-delivery-date' )                },
	{ value: 'cy-GB', label: __( 'Welsh', 'order-delivery-date' )                },
	{ value: 'da',    label: __( 'Danish', 'order-delivery-date' )               },
	{ value: 'de',    label: __( 'German', 'order-delivery-date' )               },
	{ value: 'et',    label: __( 'Estonian', 'order-delivery-date' )             },
	{ value: 'el',    label: __( 'Greek', 'order-delivery-date' )                },
	{ value: 'en-AU', label: __( 'English (Australia)', 'order-delivery-date' )  },
	{ value: 'en-NZ', label: __( 'English (New Zealand)', 'order-delivery-date' )},
	{ value: 'en-GB', label: __( 'English (UK)', 'order-delivery-date' )         },
	{ value: 'es',    label: __( 'Spanish', 'order-delivery-date' )              },
	{ value: 'eo',    label: __( 'Esperanto', 'order-delivery-date' )            },
	{ value: 'eu',    label: __( 'Basque', 'order-delivery-date' )               },
	{ value: 'fo',    label: __( 'Faroese', 'order-delivery-date' )              },
	{ value: 'fr',    label: __( 'French', 'order-delivery-date' )               },
	{ value: 'fr-CH', label: __( 'French Swiss', 'order-delivery-date' )         },
	{ value: 'gl',    label: __( 'Galician', 'order-delivery-date' )             },
	{ value: 'sq',    label: __( 'Albanian', 'order-delivery-date' )             },
	{ value: 'ko',    label: __( 'Korean', 'order-delivery-date' )               },
	{ value: 'he',    label: __( 'Hebrew', 'order-delivery-date' )               },
	{ value: 'hi',    label: __( 'Hindi (India)', 'order-delivery-date' )        },
	{ value: 'hr',    label: __( 'Croatian', 'order-delivery-date' )             },
	{ value: 'hy',    label: __( 'Armenian', 'order-delivery-date' )             },
	{ value: 'is',    label: __( 'Icelandic', 'order-delivery-date' )            },
	{ value: 'it',    label: __( 'Italian', 'order-delivery-date' )              },
	{ value: 'ka',    label: __( 'Georgian', 'order-delivery-date' )             },
	{ value: 'km',    label: __( 'Khmer', 'order-delivery-date' )                },
	{ value: 'lv',    label: __( 'Latvian', 'order-delivery-date' )              },
	{ value: 'lt',    label: __( 'Lithuanian', 'order-delivery-date' )           },
	{ value: 'mk',    label: __( 'Macedonian', 'order-delivery-date' )           },
	{ value: 'hu',    label: __( 'Hungarian', 'order-delivery-date' )            },
	{ value: 'ml',    label: __( 'Malayam', 'order-delivery-date' )              },
	{ value: 'nl',    label: __( 'Dutch', 'order-delivery-date' )                },
	{ value: 'ja',    label: __( 'Japanese', 'order-delivery-date' )             },
	{ value: 'no',    label: __( 'Norwegian', 'order-delivery-date' )            },
	{ value: 'th',    label: __( 'Thai', 'order-delivery-date' )                 },
	{ value: 'pl',    label: __( 'Polish', 'order-delivery-date' )               },
	{ value: 'pt-pt', label: __( 'Portuguese', 'order-delivery-date' )           },
	{ value: 'pt',    label: __( 'Portuguese', 'order-delivery-date' )           },
	{ value: 'pt-BR', label: __( 'Portuguese (Brazil)', 'order-delivery-date' )  },
	{ value: 'ro',    label: __( 'Romanian', 'order-delivery-date' )             },
	{ value: 'rm',    label: __( 'Romansh', 'order-delivery-date' )              },
	{ value: 'ru',    label: __( 'Russian', 'order-delivery-date' )              },
	{ value: 'sk',    label: __( 'Slovak', 'order-delivery-date' )               },
	{ value: 'sl',    label: __( 'Slovenian', 'order-delivery-date' )            },
	{ value: 'sr',    label: __( 'Serbian', 'order-delivery-date' )              },
	{ value: 'fi',    label: __( 'Finnish', 'order-delivery-date' )              },
	{ value: 'sv',    label: __( 'Swedish', 'order-delivery-date' )              },
	{ value: 'ta',    label: __( 'Tamil', 'order-delivery-date' )                },
	{ value: 'vi',    label: __( 'Vietnamese', 'order-delivery-date' )           },
	{ value: 'tr',    label: __( 'Turkish', 'order-delivery-date' )              },
	{ value: 'uk',    label: __( 'Ukrainian', 'order-delivery-date' )            },
	{ value: 'zh-HK', label: __( 'Chinese (Hong Kong)', 'order-delivery-date' )  },
	{ value: 'zh-CN', label: __( 'Chinese (Simplified)', 'order-delivery-date' ) },
	{ value: 'zh-TW', label: __( 'Chinese (Traditional)', 'order-delivery-date' )},
];

// ── Default values ─────────────────────────────────────────────────────────────

const DEFAULT_VALUES = {
	orddd_lite_language_selected:          'en-GB',
	orddd_lite_delivery_date_format:       'd MM, yy',
	orddd_lite_delivery_time_format:       '1',
	orddd_lite_start_of_week:              '0',
	orddd_lite_number_of_months:           '1',
	orddd_lite_calendar_theme:             'smoothness',
	orddd_lite_available_dates_color:      '#77c04b',
	orddd_lite_booked_dates_color:         '#f85959',
	orddd_lite_holiday_color:              '#ff0000',
	orddd_lite_show_partially_booked_dates: false,
};

const upgradeUrl =
	window.ordddAdminData?.upgradeUrl ??
	'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/';

// ── LockedToggle ──────────────────────────────────────────────────────────────

const LockedToggle = () => (
	<div className="orddd-locked-toggle">
		<span className="orddd-locked-toggle__badge">
			{ __( 'Pro', 'order-delivery-date' ) }
		</span>
		<a
			href={ upgradeUrl }
			target="_blank"
			rel="noopener noreferrer"
			className="orddd-locked-toggle__link"
		>
			{ __( 'Upgrade to Pro', 'order-delivery-date' ) }
		</a>
	</div>
);

// ── ColorInput ────────────────────────────────────────────────────────────────

const ColorInput = ( { value, onChange } ) => (
	<div className="ap-color-input">
		<input
			type="color"
			className="ap-color-swatch"
			value={ value }
			onChange={ ( e ) => onChange( e.target.value ) }
		/>
		<TextControl
			className="ap-color-text"
			value={ value }
			onChange={ onChange }
			placeholder="#000000"
			__nextHasNoMarginBottom
		/>
	</div>
);

// ── CalendarAppearance ────────────────────────────────────────────────────────

const CalendarAppearance = () => {
	const [ loading, setLoading ] = useState( true );
	const [ saving,  setSaving  ] = useState( false );
	const [ notice,  setNotice  ] = useState( null );

	const { control, handleSubmit, reset } = useForm( { defaultValues: DEFAULT_VALUES } );

	// Watch theme to update the inline preview.
	const selectedTheme = useWatch( { control, name: 'orddd_lite_calendar_theme' } );

	// Inline datepicker preview.
	const previewRef     = useRef( null );
	const themeLinkRef   = useRef( null );
	const pluginUrl      = ( window.ordddAdminData || {} ).plugin_url || '';

	// Swap theme CSS whenever the selected theme changes.
	useEffect( () => {
		if ( ! pluginUrl || ! selectedTheme ) return;

		const href = `${ pluginUrl }css/themes/${ selectedTheme ?? 'smoothness' }/jquery-ui.css`;

		if ( ! themeLinkRef.current ) {
			const link = document.createElement( 'link' );
			link.rel  = 'stylesheet';
			link.id   = 'orddd-theme-preview-css';
			link.href = href;
			document.head.appendChild( link );
			themeLinkRef.current = link;
		} else {
			themeLinkRef.current.href = href;
		}

		return () => {
			// Leave the link in the DOM while the component is alive; remove on unmount.
		};
	}, [ selectedTheme, pluginUrl ] );

	// Remove the injected link when the component unmounts.
	useEffect( () => {
		return () => {
			if ( themeLinkRef.current ) {
				themeLinkRef.current.remove();
				themeLinkRef.current = null;
			}
		};
	}, [] );

	// Initialise / re-initialise inline jQuery UI datepicker.
	// Depends on `loading` so it runs once the spinner clears and the ref mounts.
	useEffect( () => {
		if ( loading ) return;
		const el = previewRef.current;
		if ( ! el || ! window.jQuery ) return;
		const $ = window.jQuery;
		$( el ).datepicker( 'destroy' );
		$( el ).datepicker( { inline: true, showOtherMonths: true } );
	}, [ loading, selectedTheme ] );

	// ── Fetch ──────────────────────────────────────────────────────────────────

	useEffect( () => {
		apiFetch( { path: '/orddd/v1/settings/appearance/calendar' } )
			.then( ( data ) => {
				const ca = data?.data ?? data ?? {};
				reset( {
					orddd_lite_language_selected:           ca.orddd_lite_language_selected                    ?? 'en-GB',
					orddd_lite_delivery_date_format:        ca.orddd_lite_delivery_date_format                 ?? 'd MM, yy',
					orddd_lite_delivery_time_format:        ca.orddd_lite_delivery_time_format                 ?? '1',
					orddd_lite_start_of_week:               String( ca.orddd_lite_start_of_week               ?? '0' ),
					orddd_lite_number_of_months:            String( ca.orddd_lite_number_of_months            ?? '1' ),
					orddd_lite_calendar_theme:              ca.orddd_lite_calendar_theme                       ?? 'smoothness',
					orddd_lite_available_dates_color:       ca.orddd_lite_available_dates_color                ?? '#77c04b',
					orddd_lite_booked_dates_color:          ca.orddd_booked_color                              ?? '#f85959',
					orddd_lite_holiday_color:               ca.orddd_lite_holiday_color                        ?? '#ff0000',
					orddd_lite_show_partially_booked_dates: ca.orddd_lite_show_partially_booked_dates          === 'on',
				} );
			} )
			.catch( () => setNotice( { type: 'error', message: __( 'Could not load settings. Please refresh and try again.', 'order-delivery-date' ) } ) )
			.finally( () => setLoading( false ) );
	}, [] );

	// ── Save ───────────────────────────────────────────────────────────────────

	const onSubmit = async ( values ) => {
		setSaving( true );
		setNotice( null );

		try {
			await apiFetch( {
				path:   '/orddd/v1/settings/appearance/calendar',
				method: 'POST',
				data:   {
					orddd_lite_language_selected:           values.orddd_lite_language_selected,
					orddd_lite_delivery_date_format:        values.orddd_lite_delivery_date_format,
					orddd_lite_delivery_time_format:        values.orddd_lite_delivery_time_format,
					orddd_lite_start_of_week:               values.orddd_lite_start_of_week,
					orddd_lite_number_of_months:            values.orddd_lite_number_of_months,
					orddd_lite_calendar_theme:              values.orddd_lite_calendar_theme,
					orddd_lite_available_dates_color:       values.orddd_lite_available_dates_color,
					orddd_booked_color:                     values.orddd_lite_booked_dates_color,
					orddd_lite_holiday_color:               values.orddd_lite_holiday_color,
					orddd_lite_show_partially_booked_dates: values.orddd_lite_show_partially_booked_dates ? 'on' : '',
				},
			} );

		} catch ( err ) {
			setNotice( {
				type:    'error',
				message: err?.message ?? __( 'Failed to save settings. Please try again.', 'order-delivery-date' ),
			} );
			throw err;
		} finally {
			setSaving( false );
		}
	};

	// ── Loading ───────────────────────────────────────────────────────────────

	if ( loading ) {
		return <div className="bd-loading"><Spinner /></div>;
	}

	// ── Render ────────────────────────────────────────────────────────────────

	return (
		<div className="orddd-page-wrap fa-page">
			{ notice && (
				<Notice status={ notice.type } isDismissible onRemove={ () => setNotice( null ) }>
					{ notice.message }
				</Notice>
			) }

			<SettingsPanel
				title={ __( 'Calendar Appearance Settings', 'order-delivery-date' ) }
				subtitle={ __( "Fit your needs by tailoring the calendar appearance from top to bottom (or let's talk calendar - from Jan through Dec!)", 'order-delivery-date' ) }
				noCard
				onSave={ handleSubmit( onSubmit ) }
				isSaving={ saving }
			>
				<AccordionCard title={ __( 'Calendar Appearance', 'order-delivery-date' ) }>
					<SettingRow
						label={ __( 'Calendar Language:', 'order-delivery-date' ) }
						tooltip={ __( 'The language used for month names, day names, and other calendar labels.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_language_selected"
							control={ control }
							render={ ( { field } ) => (
								<SelectControl
									value={ field.value }
									options={ LANGUAGE_OPTIONS }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Date Format:', 'order-delivery-date' ) }
						tooltip={ __( 'How the selected delivery date is formatted in the order field and confirmation.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_delivery_date_format"
							control={ control }
							render={ ( { field } ) => (
								<SelectControl
									value={ field.value }
									options={ DATE_FORMAT_OPTIONS }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Time Format:', 'order-delivery-date' ) }
						tooltip={ __( 'Whether time slots are shown in 12-hour (AM/PM) or 24-hour format.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_delivery_time_format"
							control={ control }
							render={ ( { field } ) => (
								<SelectControl
									value={ field.value }
									options={ TIME_FORMAT_OPTIONS }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'First Day of Week:', 'order-delivery-date' ) }
						tooltip={ __( 'Choose which day the calendar week starts on.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_start_of_week"
							control={ control }
							render={ ( { field } ) => (
								<SelectControl
									value={ field.value }
									options={ START_OF_WEEK_OPTIONS }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Number of Months:', 'order-delivery-date' ) }
						tooltip={ __( 'How many calendar months to show at once on the checkout page.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_number_of_months"
							control={ control }
							render={ ( { field } ) => (
								<SelectControl
									value={ field.value }
									options={ NUMBER_OF_MONTHS_OPTIONS }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Theme:', 'order-delivery-date' ) }
						tooltip={ __( 'The visual theme applied to the jQuery UI datepicker calendar widget.', 'order-delivery-date' ) }
					>
						<div>
							<Controller
								name="orddd_lite_calendar_theme"
								control={ control }
								render={ ( { field } ) => (
									<SelectControl
										value={ field.value }
										options={ CALENDAR_THEME_OPTIONS }
										onChange={ field.onChange }
										__nextHasNoMarginBottom
									/>
								) }
							/>
							<div className="orddd-theme-preview">
								<span className="orddd-theme-preview-label">{ __( 'Preview Theme:', 'order-delivery-date' ) }</span>
								<div ref={ previewRef } className="orddd-theme-preview-calendar" />
							</div>
						</div>
					</SettingRow>

					<SettingRow
						label={ __( 'Display availability on date:', 'order-delivery-date' ) }
						tooltip={ __( 'Show the availability widget that displays delivery dates on the frontend. Available in Pro.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

				</AccordionCard>

				<AccordionCard title={ __( 'Color Code Your Calendar', 'order-delivery-date' ) }>
					<SettingRow
						label={ __( 'Show partially booked dates:', 'order-delivery-date' ) }
						tooltip={ __( 'Highlight dates that still have some delivery slots available on the calendar.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_show_partially_booked_dates"
							control={ control }
							render={ ( { field } ) => (
								<ToggleControl
									checked={ field.value }
									onChange={ field.onChange }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Available dates:', 'order-delivery-date' ) }
						tooltip={ __( 'Colour used to highlight dates that are open for delivery.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_available_dates_color"
							control={ control }
							render={ ( { field } ) => (
								<ColorInput value={ field.value } onChange={ field.onChange } />
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Booked dates:', 'order-delivery-date' ) }
						tooltip={ __( 'Colour used to indicate fully booked dates.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_booked_dates_color"
							control={ control }
							render={ ( { field } ) => (
								<ColorInput value={ field.value } onChange={ field.onChange } />
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Holidays:', 'order-delivery-date' ) }
						tooltip={ __( 'Colour used to mark holiday dates when delivery is unavailable.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_holiday_color"
							control={ control }
							render={ ( { field } ) => (
								<ColorInput value={ field.value } onChange={ field.onChange } />
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Cut-off time dates:', 'order-delivery-date' ) }
						tooltip={ __( 'Colour used to indicate dates that are past the cut-off time for ordering. Available in Pro.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

				</AccordionCard>
			</SettingsPanel>
		</div>
	);
};

export default CalendarAppearance;
