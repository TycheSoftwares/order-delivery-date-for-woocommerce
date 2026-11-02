/**
 * SettingsForm — reusable Card-based form layout.
 *
 * Exports
 *   SettingsFormCard       — Card shell with optional title
 *   SettingsFormRow        — Divider + labeled row (220px label | 1fr field)
 *   DatePickerField        — WP DatePicker calendar wrapped in a Dropdown trigger
 *   MultiSelectDropdown    — Checkbox-list dropdown for multi-value selection
 *   SettingsFormSaveBtn    — Styled "Save Settings" button
 *
 * Usage
 *   import SettingsFormCard, {
 *     SettingsFormRow, DatePickerField, MultiSelectDropdown, SettingsFormSaveBtn,
 *   } from '../../components/SettingsForm';
 */

import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import {
	Card,
	CardBody,
	CardDivider,
	Tooltip,
	Icon,
	Button,
	DatePicker,
	Dropdown,
} from '@wordpress/components';
import { info, calendar } from '@wordpress/icons';

const calData = () => window.orddd_calendar_js || {};

// ── SettingsFormCard ──────────────────────────────────────────────────────────
// Wraps children in a WP Card with an optional title row.

export const SettingsFormCard = ( { title, children, className = '' } ) => (
	<Card className={ `sf-card ${ className }`.trim() } size="none">
		{ title && (
			<CardBody className="sf-card__title-row">
				<h2 className="sf-card__title">{ title }</h2>
			</CardBody>
		) }
		{ children }
	</Card>
);

// ── SettingsFormRow ───────────────────────────────────────────────────────────
// Renders a CardDivider followed by a two-column CardBody row.
//
// Props
//   label    — bold left-column text
//   tooltip  — ⓘ icon tooltip text (always rendered when provided)
//   hint     — small text shown beside the ⓘ icon (col layout only)
//   layout   — 'row' (default) | 'col'
//              'row': icon sits inline with children
//              'col': hint bar (icon + text) stacks above children

export const SettingsFormRow = ( { label, tooltip, hint, layout = 'row', children } ) => (
	<>
		<CardDivider />
		<CardBody className="sf-row">
			<div className="sf-row__label">{ label }</div>
			<div className={ `sf-row__field${ layout === 'col' ? ' sf-row__field--col' : '' }` }>

				{ /* Row layout — icon sits inline to the left of children */ }
				{ layout === 'row' && tooltip && (
					<Tooltip text={ tooltip }>
						<span className="sf-row__icon" tabIndex={ 0 }>
							<Icon icon={ info } size={ 16 } />
						</span>
					</Tooltip>
				) }

				{ /* Col layout — hint bar (icon + optional text) stacks above children */ }
				{ layout === 'col' && ( tooltip || hint ) && (
					<div className="sf-row__hint">
						{ tooltip && (
							<Tooltip text={ tooltip }>
								<span className="sf-row__icon" tabIndex={ 0 }>
									<Icon icon={ info } size={ 16 } />
								</span>
							</Tooltip>
						) }
						{ hint && <span className="sf-row__hint-text">{ hint }</span> }
					</div>
				) }

				{ children }
			</div>
		</CardBody>
	</>
);

// ── DatePickerField ───────────────────────────────────────────────────────────
// Shows a styled trigger button; clicking it opens the WP DatePicker calendar
// in a Dropdown popover. Selecting a date closes the popover automatically.
//
// Props
//   value       — YYYY-MM-DD string, or empty string
//   onChange    — called with YYYY-MM-DD string when a date is picked
//   placeholder — text shown when value is empty (default 'Select date')

export const DatePickerField = ( { value, onChange, placeholder = __( 'Select date', 'order-delivery-date' ) } ) => {
	const display = value ? value.slice( 0, 10 ) : '';
	return (
		<Dropdown
			className="sf-date-dropdown"
			popoverProps={ { placement: 'bottom-start' } }
			renderToggle={ ( { isOpen, onToggle } ) => (
				<button
					type="button"
					className={ `sf-date-trigger${ isOpen ? ' is-open' : '' }` }
					onClick={ onToggle }
					aria-expanded={ isOpen }
					aria-haspopup="dialog"
				>
					<span className="sf-date-trigger__text">
						{ display || placeholder }
					</span>
					<Icon icon={ calendar } size={ 14 } />
				</button>
			) }
			renderContent={ ( { onClose } ) => (
				<div className="sf-date-popover">
					<DatePicker
						currentDate={ value || undefined }
						startOfWeek={ calData().first_day_of_week ?? 0 }
						onChange={ ( date ) => {
							// DatePicker emits a full ISO string; keep only YYYY-MM-DD
							onChange( date ? date.slice( 0, 10 ) : '' );
							onClose();
						} }
					/>
				</div>
			) }
		/>
	);
};

// ── MultiDatePickerField ──────────────────────────────────────────────────────
// Calendar popover that stays open, letting the user pick multiple dates.
// Each click toggles that date in/out of the array. Chips below show selections.
//
// Props
//   value    — array of YYYY-MM-DD strings
//   onChange — called with the updated array

export const MultiDatePickerField = ( { value = [], onChange } ) => {
	const [ isOpen, setIsOpen ] = useState( false );

	const toggleDate = ( date ) => {
		const d = date ? date.slice( 0, 10 ) : '';
		if ( ! d ) return;
		onChange( value.includes( d ) ? value.filter( ( v ) => v !== d ) : [ ...value, d ] );
	};

	const removeDate = ( d ) => onChange( value.filter( ( v ) => v !== d ) );

	const triggerLabel = value.length === 0
		? __( 'Select dates', 'order-delivery-date' )
		: sprintf( __( '%d date(s) selected', 'order-delivery-date' ), value.length );

	return (
		<div className="sf-multi-date">
			<button
				type="button"
				className={ `sf-date-trigger${ isOpen ? ' is-open' : '' }` }
				onClick={ () => setIsOpen( ( v ) => ! v ) }
				aria-expanded={ isOpen }
			>
				<span className="sf-date-trigger__text">{ triggerLabel }</span>
				<Icon icon={ calendar } size={ 14 } />
			</button>

			{ isOpen && (
				<div className="sf-multi-date-popover">
					<DatePicker
						currentDate={ value[ value.length - 1 ] || undefined }
						startOfWeek={ calData().first_day_of_week ?? 0 }
						onChange={ toggleDate }
					/>
					<div className="sf-multi-date-popover__footer">
						<Button variant="primary" isSmall onClick={ () => setIsOpen( false ) }>
							{ __( 'Done', 'order-delivery-date' ) }
						</Button>
					</div>
				</div>
			) }

			{ value.length > 0 && (
				<div className="sf-multi-date-chips">
					{ value.map( ( d ) => (
						<span key={ d } className="sf-multi-date-chip">
							{ d }
							<button
								type="button"
								className="sf-multi-date-chip__remove"
								onClick={ () => removeDate( d ) }
								aria-label={ sprintf( __( 'Remove %s', 'order-delivery-date' ), d ) }
							>
								×
							</button>
						</span>
					) ) }
				</div>
			) }
		</div>
	);
};

// ── MultiSelectDropdown ───────────────────────────────────────────────────────
// Checkbox-list dropdown that stays open during multi-selection.
//
// Props
//   options     — array of { value, label }; items with value === '' are skipped
//   value       — array of selected values
//   onChange    — called with new array of selected values
//   placeholder — text shown when nothing is selected (default 'Select…')

export const MultiSelectDropdown = ( { options, value = [], onChange, placeholder = __( 'Select\u2026', 'order-delivery-date' ) } ) => {
	const realOptions = options.filter( ( o ) => o.value !== '' );

	const toggle = ( optValue ) => {
		if ( value.includes( optValue ) ) {
			onChange( value.filter( ( v ) => v !== optValue ) );
		} else {
			onChange( [ ...value, optValue ] );
		}
	};

	const selectedLabels = options
		.filter( ( o ) => value.includes( o.value ) )
		.map( ( o ) => o.label );

	const display = selectedLabels.length === 0
		? placeholder
		: selectedLabels.length === 1
			? selectedLabels[ 0 ]
			: sprintf(
				/* translators: %d: number of selected items */
				__( '%d selected', 'order-delivery-date' ),
				selectedLabels.length
			);

	return (
		<Dropdown
			className="sf-msd"
			popoverProps={ { placement: 'bottom-start' } }
			renderToggle={ ( { isOpen, onToggle } ) => (
				<button
					type="button"
					className={ `sf-msd__trigger${ isOpen ? ' is-open' : '' }` }
					onClick={ onToggle }
					aria-expanded={ isOpen }
					aria-haspopup="listbox"
				>
					<span className="sf-msd__trigger-text">{ display }</span>
					<span className="sf-msd__chevron" aria-hidden="true">▾</span>
				</button>
			) }
			renderContent={ () => (
				<div className="sf-msd__menu" role="listbox" aria-multiselectable="true">
					{ realOptions.map( ( opt ) => (
						<label
							key={ opt.value }
							className={ `sf-msd__option${ value.includes( opt.value ) ? ' is-checked' : '' }` }
						>
							<input
								type="checkbox"
								className="sf-msd__checkbox"
								checked={ value.includes( opt.value ) }
								onChange={ () => toggle( opt.value ) }
							/>
							{ opt.label }
						</label>
					) ) }
				</div>
			) }
		/>
	);
};

// ── SettingsFormSaveBtn ───────────────────────────────────────────────────────
// Pre-styled "Save Settings" secondary button.

export const SettingsFormSaveBtn = ( { onClick, isBusy, disabled, children = __( 'Save Settings', 'order-delivery-date' ) } ) => (
	<Button
		className="sf-save-btn"
		variant="secondary"
		onClick={ onClick }
		isBusy={ isBusy }
		disabled={ disabled }
	>
		{ children }
	</Button>
);

export default SettingsFormCard;
