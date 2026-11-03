/**
 * WordPress dependencies
 */
import { FormTokenField } from '@wordpress/components';
import InlineRadios from '../../../components/ui/InlineRadios';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import DataTable from '../../../components/DataTable';
import { UpgradePrompt } from '../../../components/ProNotice';
import SettingsFormCard, {
	SettingsFormRow,
	MultiDatePickerField,
	SettingsFormSaveBtn,
} from '../../../components/SettingsForm';

// ── Constants ─────────────────────────────────────────────────────────────────

const COLUMNS = [
	{ key: 'title', label: __( 'Shipping Methods',    'order-delivery-date' ) },
	{ key: 'date',  label: __( 'Delivery Days/Dates', 'order-delivery-date' ) },
];

const BULK_ACTIONS = [
	{ value: 'delete', label: __( 'Delete', 'order-delivery-date' ) },
];

const DISABLE_FOR_OPTIONS = [
	{ value: 'dates',    label: __( 'Dates',    'order-delivery-date' ) },
	{ value: 'weekdays', label: __( 'Weekdays', 'order-delivery-date' ) },
];

const WEEKDAY_OPTIONS = [
	{ value: 'orddd_weekday_0', label: __( 'Sunday',    'order-delivery-date' ) },
	{ value: 'orddd_weekday_1', label: __( 'Monday',    'order-delivery-date' ) },
	{ value: 'orddd_weekday_2', label: __( 'Tuesday',   'order-delivery-date' ) },
	{ value: 'orddd_weekday_3', label: __( 'Wednesday', 'order-delivery-date' ) },
	{ value: 'orddd_weekday_4', label: __( 'Thursday',  'order-delivery-date' ) },
	{ value: 'orddd_weekday_5', label: __( 'Friday',    'order-delivery-date' ) },
	{ value: 'orddd_weekday_6', label: __( 'Saturday',  'order-delivery-date' ) },
];

const EMPTY_FORM = {
	block_shipping_for: 'dates',
	shipping_methods:   [],
	delivery_days:      [],
};

// ── AddShippingForm ───────────────────────────────────────────────────────────

const AddShippingForm = ( { form, setForm } ) => {
	const setField = ( key ) => ( value ) =>
		setForm( ( prev ) => ( { ...prev, [ key ]: value } ) );

	const isDates = form.block_shipping_for === 'dates';

	const allMethods       = ( window.ordddAdminData?.all_shipping_methods || [] );
	const methodOpts       = allMethods.map( ( m ) => ( { value: m.method_key, label: m.title } ) );
	const toMethodLabels   = ( values ) => values.map( ( v ) => methodOpts.find( ( o ) => o.value === v )?.label ?? v );
	const fromMethodLabels = ( labels ) => labels.map( ( l ) => methodOpts.find( ( o ) => o.label === l )?.value ?? l );

	const toWeekdayLabels   = ( values ) => values.map( ( v ) => WEEKDAY_OPTIONS.find( ( o ) => o.value === v )?.label ?? v );
	const fromWeekdayLabels = ( labels ) => labels.map( ( l ) => WEEKDAY_OPTIONS.find( ( o ) => o.label === l )?.value ?? l );

	return (
		<SettingsFormCard title={ __( 'Add Shipping Methods to Disable', 'order-delivery-date' ) }>

			<SettingsFormRow
				label={ __( 'Disable Shipping Methods For:', 'order-delivery-date' ) }
				tooltip={ __( 'Choose whether to disable shipping on specific dates or recurring weekdays.', 'order-delivery-date' ) }
			>
				<InlineRadios
					name="ds_block_shipping_for"
					value={ form.block_shipping_for }
					options={ DISABLE_FOR_OPTIONS }
					onChange={ ( v ) => setForm( ( prev ) => ( {
						...prev,
						block_shipping_for: v,
						delivery_days:      [],
					} ) ) }
				/>
			</SettingsFormRow>

			<SettingsFormRow
				label={ isDates ? __( 'Delivery Date:', 'order-delivery-date' ) : __( 'Weekday:', 'order-delivery-date' ) }
				tooltip={ isDates
					? __( 'Pick the dates on which the shipping method should be disabled.', 'order-delivery-date' )
					: __( 'Select the weekdays on which the shipping method should be disabled.', 'order-delivery-date' ) }
				hint={ isDates ? __( 'Select Date', 'order-delivery-date' ) : __( 'Select Weekday', 'order-delivery-date' ) }
			>
				{ isDates ? (
					<MultiDatePickerField
						value={ Array.isArray( form.delivery_days ) ? form.delivery_days : [] }
						onChange={ setField( 'delivery_days' ) }
					/>
				) : (
					<FormTokenField
						label=""
						help=""
						suggestions={ WEEKDAY_OPTIONS.map( ( o ) => o.label ) }
						value={ toWeekdayLabels( Array.isArray( form.delivery_days ) ? form.delivery_days : [] ) }
						onChange={ ( labels ) => setField( 'delivery_days' )( fromWeekdayLabels( labels ) ) }
						__nextHasNoMarginBottom
						__experimentalExpandOnFocus
					/>
				) }
			</SettingsFormRow>

			<SettingsFormRow
				label={ __( 'Select Shipping Methods to Disable:', 'order-delivery-date' ) }
				tooltip={ __( 'Select the shipping methods you want to disable.', 'order-delivery-date' ) }
				hint={ __( 'Select Shipping Methods', 'order-delivery-date' ) }
			>
				<FormTokenField
					label=""
					help=""
					suggestions={ methodOpts.map( ( o ) => o.label ) }
					value={ toMethodLabels( form.shipping_methods ) }
					onChange={ ( labels ) => setField( 'shipping_methods' )( fromMethodLabels( labels ) ) }
					__nextHasNoMarginBottom
					__experimentalExpandOnFocus
				/>
			</SettingsFormRow>

			<SettingsFormRow>
				<SettingsFormSaveBtn disabled />
			</SettingsFormRow>

		</SettingsFormCard>
	);
};

// ── DisableShipping ───────────────────────────────────────────────────────────

const DisableShipping = ( { locked = false } ) => {
	return (
		<div className="orddd-page-wrap osp-wrap">
			{ locked && <UpgradePrompt message={ __( 'Unlock Disable Shipping settings by upgrading to Pro.', 'order-delivery-date' ) } /> }

			<div className={ locked ? 'orddd-locked-panel' : undefined }>
				<div className="osp-page-header">
					<div className="osp-page-header-text">
						<h1 className="osp-title">{ __( 'Disable Shipping', 'order-delivery-date' ) }</h1>
						<p className="osp-subtitle">
							{ __( 'Hide the shipping methods on checkout based on selected weekdays or specific dates.', 'order-delivery-date' ) }
						</p>
					</div>
				</div>

				<DataTable
					columns={ COLUMNS }
					rows={ [] }
					rowKey={ ( row ) => `${ row.methodKey }:${ row.dateKey }` }
					loading={ false }
					renderActions={ () => null }
					showForm={ false }
					onToggleForm={ () => {} }
					renderForm={
						<AddShippingForm
							form={ EMPTY_FORM }
							setForm={ () => {} }
						/>
					}
					bulkActions={ BULK_ACTIONS }
					onBulkApply={ () => {} }
					emptyMessage={ __( 'No disabled shipping rules found.', 'order-delivery-date' ) }
				/>
			</div>
		</div>
	);
};

export default DisableShipping;
