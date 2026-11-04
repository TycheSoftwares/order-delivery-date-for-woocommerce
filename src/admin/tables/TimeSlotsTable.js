/**
 * TimeSlotsTable – CRUD table for orddd_lite_delivery_time_slot_log.
 */

import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { SelectControl, TextControl } from '@wordpress/components';

const weekdays = window.ordddAdminData?.weekdays ?? {};

const EMPTY_ROW = {
	tv: 'weekdays',
	dd: '[]',
	fh: '9',
	fm: '00',
	th: '10',
	tm: '00',
	lockout: '',
	additional_charges: '',
	charges_type: 'fixed',
	additional_charges_label: '',
};

/**
 * @param {Object}   props
 * @param {Array}    props.slots    Array of slot objects.
 * @param {Function} props.onChange Called with updated slots array.
 */
export default function TimeSlotsTable( { slots = [], onChange } ) {
	const [ adding, setAdding ] = useState( false );
	const [ newRow, setNewRow ] = useState( { ...EMPTY_ROW } );

	const weekdayOptions = Object.entries( weekdays ).map( ( [ value, label ] ) => ( {
		value,
		label,
	} ) );

	const formatTime = ( h, m ) =>
		`${ String( h ).padStart( 2, '0' ) }:${ String( m ).padStart( 2, '0' ) }`;

	const handleAdd = () => {
		const updated = [ ...slots, { ...newRow } ];
		onChange( updated );
		setNewRow( { ...EMPTY_ROW } );
		setAdding( false );
	};

	const handleDelete = ( index ) => {
		const updated = slots.filter( ( _, i ) => i !== index );
		onChange( updated );
	};

	return (
		<div className="orddd-lite-table-wrap">
			<table className="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>{ __( 'For', 'order-delivery-date' ) }</th>
						<th>{ __( 'Days', 'order-delivery-date' ) }</th>
						<th>{ __( 'From', 'order-delivery-date' ) }</th>
						<th>{ __( 'To', 'order-delivery-date' ) }</th>
						<th>{ __( 'Max Orders', 'order-delivery-date' ) }</th>
						<th>{ __( 'Actions', 'order-delivery-date' ) }</th>
					</tr>
				</thead>
				<tbody>
					{ slots.length === 0 && (
						<tr>
							<td colSpan="6">
								{ __( 'No time slots defined.', 'order-delivery-date' ) }
							</td>
						</tr>
					) }
					{ slots.map( ( slot, i ) => {
						const days = JSON.parse( slot.dd || '[]' );
						const dayLabels = days
							.map( ( d ) => weekdays[ d ] ?? d )
							.join( ', ' );
						return (
							<tr key={ i }>
								<td>{ slot.tv === 'weekdays' ? __( 'Weekdays', 'order-delivery-date' ) : __( 'Dates', 'order-delivery-date' ) }</td>
								<td>{ dayLabels }</td>
								<td>{ formatTime( slot.fh, slot.fm ) }</td>
								<td>{ formatTime( slot.th, slot.tm ) }</td>
								<td>{ slot.lockout || '—' }</td>
								<td>
									<button
										type="button"
										className="button button-small button-link-delete"
										onClick={ () => handleDelete( i ) }
									>
										{ __( 'Delete', 'order-delivery-date' ) }
									</button>
								</td>
							</tr>
						);
					} ) }
				</tbody>
			</table>

			{ adding ? (
				<div className="orddd-lite-add-row">
					<h4>{ __( 'Add Time Slot', 'order-delivery-date' ) }</h4>
					<SelectControl
						label={ __( 'For', 'order-delivery-date' ) }
						value={ newRow.tv }
						options={ [
							{ value: 'weekdays', label: __( 'Weekdays', 'order-delivery-date' ) },
						] }
						onChange={ ( v ) => setNewRow( { ...newRow, tv: v } ) }
					/>
					<SelectControl
						label={ __( 'Day', 'order-delivery-date' ) }
						value={ JSON.parse( newRow.dd || '[]' )[ 0 ] ?? '' }
						options={ [ { value: '', label: __( '— Select —', 'order-delivery-date' ) }, ...weekdayOptions ] }
						onChange={ ( v ) =>
							setNewRow( { ...newRow, dd: JSON.stringify( [ v ] ) } )
						}
					/>
					<TextControl
						label={ __( 'From (HH)', 'order-delivery-date' ) }
						type="number"
						value={ newRow.fh }
						onChange={ ( v ) => setNewRow( { ...newRow, fh: v } ) }
					/>
					<TextControl
						label={ __( 'From (MM)', 'order-delivery-date' ) }
						type="number"
						value={ newRow.fm }
						onChange={ ( v ) => setNewRow( { ...newRow, fm: v } ) }
					/>
					<TextControl
						label={ __( 'To (HH)', 'order-delivery-date' ) }
						type="number"
						value={ newRow.th }
						onChange={ ( v ) => setNewRow( { ...newRow, th: v } ) }
					/>
					<TextControl
						label={ __( 'To (MM)', 'order-delivery-date' ) }
						type="number"
						value={ newRow.tm }
						onChange={ ( v ) => setNewRow( { ...newRow, tm: v } ) }
					/>
					<TextControl
						label={ __( 'Max Orders (0 = unlimited)', 'order-delivery-date' ) }
						type="number"
						value={ newRow.lockout }
						onChange={ ( v ) => setNewRow( { ...newRow, lockout: v } ) }
					/>
					<div className="orddd-lite-add-row__actions">
						<button type="button" className="button button-primary" onClick={ handleAdd }>
							{ __( 'Add', 'order-delivery-date' ) }
						</button>
						<button
							type="button"
							className="button"
							onClick={ () => setAdding( false ) }
						>
							{ __( 'Cancel', 'order-delivery-date' ) }
						</button>
					</div>
				</div>
			) : (
				<button
					type="button"
					className="button"
					onClick={ () => setAdding( true ) }
				>
					{ __( '+ Add Time Slot', 'order-delivery-date' ) }
				</button>
			) }
		</div>
	);
}
