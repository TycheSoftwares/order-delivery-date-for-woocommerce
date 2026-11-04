/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import {
	ToggleControl,
	TextControl,
	TextareaControl,
	Notice,
	Spinner,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { useForm, Controller } from 'react-hook-form';

/**
 * Internal dependencies
 */
import SettingsPanel, { AccordionCard, SettingRow } from '../../../components/SettingsPanel';

// ── Constants ─────────────────────────────────────────────────────────────────

const PLACEMENT_OPTIONS = [
	{ value: 'billing_section',        label: __( 'In Billing Section', 'order-delivery-date' )                   },
	{ value: 'shipping_section',       label: __( 'In Shipping Section', 'order-delivery-date' )                  },
	{ value: 'before_order_notes',     label: __( 'Before Order Notes', 'order-delivery-date' )                   },
	{ value: 'after_order_notes',      label: __( 'After Order Notes', 'order-delivery-date' )                    },
	{ value: 'after_your_order_table', label: __( 'Between Your Order & Payment Section', 'order-delivery-date' ) },
];


const DEFAULT_VALUES = {
	orddd_lite_delivery_date_field_placeholder:         '',
	orddd_lite_delivery_date_field_note:                '',
	orddd_lite_delivery_date_fields_on_checkout_page:   'billing_section',
	orddd_lite_delivery_date_on_cart_page:              false,
	orddd_lite_auto_populate_first_available_date:      false,
	orddd_lite_auto_populate_first_available_time_slot: false,
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

// ── FieldPlacementControl ─────────────────────────────────────────────────────

const FieldPlacementControl = ( { value, onChange } ) => (
	<div className="fa-placement">
		{ PLACEMENT_OPTIONS.map( ( opt ) => (
			<label key={ opt.value } className="fa-placement__option">
				<input
					type="radio"
					className="fa-placement__radio"
					name="orddd_lite_field_placement"
					value={ opt.value }
					checked={ value === opt.value }
					onChange={ () => onChange( opt.value ) }
				/>
				<span className="fa-placement__label">{ opt.label }</span>
			</label>
		) ) }
		<div className="fa-placement__option">
			<span className="fa-placement__label">{ __( 'Custom Hook', 'order-delivery-date' ) }</span>
			<LockedToggle />
		</div>
		<p className="fa-placement__note">
			{ __( 'Note: In Checkout Block, please add the Order Delivery Date block manually from the Edit Checkout page in any preferred section.', 'order-delivery-date' ) }
		</p>
	</div>
);

// ── FieldAppearance ───────────────────────────────────────────────────────────

const FieldAppearance = () => {
	const [ loading, setLoading ] = useState( true );
	const [ saving,  setSaving  ] = useState( false );
	const [ notice,  setNotice  ] = useState( null );

	const { control, handleSubmit, reset } = useForm( { defaultValues: DEFAULT_VALUES } );

	// ── Fetch ──────────────────────────────────────────────────────────────────

	useEffect( () => {
		apiFetch( { path: '/orddd/v1/settings/appearance/field' } )
			.then( ( fa ) => {
				reset( {
					orddd_lite_delivery_date_field_placeholder:         fa.orddd_lite_delivery_date_field_placeholder       ?? '',
					orddd_lite_delivery_date_field_note:                fa.orddd_lite_delivery_date_field_note              ?? '',
					orddd_lite_delivery_date_fields_on_checkout_page:   fa.orddd_lite_delivery_date_fields_on_checkout_page ?? 'billing_section',
					orddd_lite_delivery_date_on_cart_page:              fa.orddd_lite_delivery_date_on_cart_page            === 'on',
					orddd_lite_auto_populate_first_available_date:      fa.orddd_lite_auto_populate_first_available_date    === 'on',
					orddd_lite_auto_populate_first_available_time_slot: fa.orddd_lite_auto_populate_first_available_time_slot === 'on',
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
				path:   '/orddd/v1/settings/appearance/field',
				method: 'POST',
				data:   {
					orddd_lite_delivery_date_field_placeholder:         values.orddd_lite_delivery_date_field_placeholder,
					orddd_lite_delivery_date_field_note:                values.orddd_lite_delivery_date_field_note,
					orddd_lite_delivery_date_fields_on_checkout_page:   values.orddd_lite_delivery_date_fields_on_checkout_page,
					orddd_lite_delivery_date_on_cart_page:              values.orddd_lite_delivery_date_on_cart_page              ? 'on' : '',
					orddd_lite_auto_populate_first_available_date:      values.orddd_lite_auto_populate_first_available_date      ? 'on' : '',
					orddd_lite_auto_populate_first_available_time_slot: values.orddd_lite_auto_populate_first_available_time_slot ? 'on' : '',
				},
			} );

			setNotice( { type: 'success', message: __( 'Field appearance settings saved.', 'order-delivery-date' ) } );
		} catch ( err ) {
			setNotice( {
				type:    'error',
				message: err?.message ?? __( 'Failed to save settings. Please try again.', 'order-delivery-date' ),
			} );
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
				title={ __( 'Field Appearance Settings', 'order-delivery-date' ) }
				subtitle={ __( 'Customise how the delivery date and time slot fields appear on your checkout.', 'order-delivery-date' ) }
				noCard
				onSave={ handleSubmit( onSubmit ) }
				isSaving={ saving }
			>
				<AccordionCard title={ __( 'Field Appearance', 'order-delivery-date' ) }>
					<SettingRow
						label={ __( 'Field Placeholder Text:', 'order-delivery-date' ) }
						tooltip={ __( 'The placeholder text shown inside the delivery date input before the customer selects a date.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_delivery_date_field_placeholder"
							control={ control }
							render={ ( { field } ) => (
								<TextControl
									value={ field.value }
									onChange={ field.onChange }
									placeholder={ __( 'Choose a Date', 'order-delivery-date' ) }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Field Note Text:', 'order-delivery-date' ) }
						tooltip={ __( 'A note displayed below the delivery date field on the checkout page. HTML is allowed.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_delivery_date_field_note"
							control={ control }
							render={ ( { field } ) => (
								<TextareaControl
									className="fa-textarea"
									value={ field.value }
									onChange={ field.onChange }
									rows={ 3 }
									__nextHasNoMarginBottom
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Estimated Date Text:', 'order-delivery-date' ) }
						tooltip={ __( 'Text shown when displaying an estimated delivery date. HTML is allowed. Available in Pro.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Fields placement on the Checkout page:', 'order-delivery-date' ) }
						tooltip={ __( 'Choose where the delivery date and time fields appear on the checkout page.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_delivery_date_fields_on_checkout_page"
							control={ control }
							render={ ( { field } ) => (
								<FieldPlacementControl
									value={ field.value }
									onChange={ field.onChange }
								/>
							) }
						/>
					</SettingRow>

					<SettingRow
						label={ __( 'Calendar Display Mode:', 'order-delivery-date' ) }
						tooltip={ __( 'Choose how the delivery date calendar appears on the checkout page. Available in Pro.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Date selection field on Cart page:', 'order-delivery-date' ) }
						tooltip={ __( 'Show the delivery date selection field on the WooCommerce cart page.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_delivery_date_on_cart_page"
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
						label={ __( 'Show Dates in Dropdown:', 'order-delivery-date' ) }
						tooltip={ __( 'Display available delivery dates in a dropdown list instead of a calendar picker. Available in Pro.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Show Time Slots in List View:', 'order-delivery-date' ) }
						tooltip={ __( 'Display time slots as a vertical list instead of a dropdown. Available in Pro.', 'order-delivery-date' ) }
					>
						<LockedToggle />
					</SettingRow>

					<SettingRow
						label={ __( 'Preselect first available date:', 'order-delivery-date' ) }
						tooltip={ __( 'Automatically pre-select the earliest available delivery date when the checkout page loads.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_auto_populate_first_available_date"
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
						label={ __( 'Preselect first available time slot:', 'order-delivery-date' ) }
						tooltip={ __( 'Automatically pre-select the first available time slot when a delivery date is chosen.', 'order-delivery-date' ) }
					>
						<Controller
							name="orddd_lite_auto_populate_first_available_time_slot"
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
				</AccordionCard>
			</SettingsPanel>
		</div>
	);
};

export default FieldAppearance;
