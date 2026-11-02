/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { ToggleControl, TextControl, Notice, Spinner } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import SettingsPanel, { SettingRow } from '../../../components/SettingsPanel';
import { UpgradePrompt } from '../../../components/ProNotice';

/**
 * External dependencies
 */
import { useForm, Controller } from 'react-hook-form';

// ── Constants ─────────────────────────────────────────────────────────────────

const DEFAULT_VALUES = {
	orddd_lockout_date_quantity_based: false,
	orddd_global_lockout_custom:       false,
	orddd_global_lockout_time_slots:   '',
	orddd_default_schedule_priority:   false,
};

// ── Component ─────────────────────────────────────────────────────────────────

const CommonSettings = ( { locked = false } ) => {
	const [ loading, setLoading ] = useState( ! locked );
	const [ saving,  setSaving  ] = useState( false );
	const [ notice,  setNotice  ] = useState( null );

	const { control, handleSubmit, reset } = useForm( { defaultValues: DEFAULT_VALUES } );

	// ── Fetch ──────────────────────────────────────────────────────────────────

	useEffect( () => {
		if ( locked ) return;

		apiFetch( { path: '/orddd/v1/settings' } )
			.then( ( data ) => {
				const cs = data?.common_schedule ?? {};
				reset( {
					orddd_lockout_date_quantity_based: cs.orddd_lockout_date_quantity_based === 'on',
					orddd_global_lockout_custom:       cs.orddd_global_lockout_custom       === 'on',
					orddd_global_lockout_time_slots:   cs.orddd_global_lockout_time_slots   ?? '',
					orddd_default_schedule_priority:   cs.orddd_default_schedule_priority   === 'on',
				} );
			} )
			.catch( () => {
				setNotice( { type: 'error', message: __( 'Could not load settings. Please refresh and try again.', 'order-delivery-date' ) } );
			} )
			.finally( () => setLoading( false ) );
	}, [] );

	// ── Save ───────────────────────────────────────────────────────────────────

	const onSubmit = async ( values ) => {
		setSaving( true );
		setNotice( null );

		try {
			await apiFetch( {
				path:   '/orddd/v1/settings',
				method: 'POST',
				data:   {
					key:                               'common_schedule',
					orddd_lockout_date_quantity_based: values.orddd_lockout_date_quantity_based ? 'on' : '',
					orddd_global_lockout_custom:       values.orddd_global_lockout_custom       ? 'on' : '',
					orddd_global_lockout_time_slots:   values.orddd_global_lockout_time_slots,
					orddd_default_schedule_priority:   values.orddd_default_schedule_priority   ? 'on' : '',
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

	// ── Loading state ─────────────────────────────────────────────────────────

	if ( loading ) {
		return (
			<div className="bd-loading">
				<Spinner />
			</div>
		);
	}

	// ── Render ────────────────────────────────────────────────────────────────

	return (
		<div className="orddd-page-wrap">
			{ locked && <UpgradePrompt message={ __( 'Unlock Common Schedule Settings by upgrading to Pro.', 'order-delivery-date' ) } /> }
			{ notice && ! locked && (
				<Notice
					status={ notice.type }
					isDismissible
					onRemove={ () => setNotice( null ) }
				>
					{ notice.message }
				</Notice>
			) }

			<div className={ locked ? 'orddd-locked-panel' : undefined }>
			<SettingsPanel
				title={ __( 'Common Schedule Settings', 'order-delivery-date' ) }
				subtitle={ __( 'If you want to deliver only a certain number of orders per day, time slot or product quantity, configure the limits here.', 'order-delivery-date' ) }
				onSave={ locked ? undefined : handleSubmit( onSubmit ) }
				isSaving={ saving }
			>
				<SettingRow
					label={ __( 'Set the maximum number of deliveries per product:', 'order-delivery-date' ) }
					tooltip={ __( 'If enabled, date availability is reduced by product quantity ordered.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_lockout_date_quantity_based"
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
					label={ __( 'Use global maximum orders per day for custom settings:', 'order-delivery-date' ) }
					tooltip={ __( 'Maximum Order Deliveries per day setting will be considered for custom settings.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_global_lockout_custom"
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
					label={ __( 'Apply default delivery schedule when no priority is set:', 'order-delivery-date' ) }
					tooltip={ __( 'In case of conflicts between 2 or more delivery schedules when no priority is set, the default delivery schedule will be applied.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_default_schedule_priority"
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
					label={ __( 'Global Maximum Order Deliveries for Time slots:', 'order-delivery-date' ) }
					tooltip={ __( 'Maximum deliveries applied to all time slots if individual maximum is blank. Leave blank for unlimited.', 'order-delivery-date' ) }
				>
					<Controller
						name="orddd_global_lockout_time_slots"
						control={ control }
						render={ ( { field } ) => (
							<TextControl
								type="number"
								value={ field.value }
								onChange={ field.onChange }
								placeholder="0"
								__nextHasNoMarginBottom
							/>
						) }
					/>
				</SettingRow>

			</SettingsPanel>
			</div>
		</div>
	);
};

export default CommonSettings;
