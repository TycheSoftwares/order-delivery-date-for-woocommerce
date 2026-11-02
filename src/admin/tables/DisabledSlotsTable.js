/**
 * DisabledSlotsTable – CRUD table for orddd_lite_disable_time_slot_log.
 * Uses the REST API block-time-slot endpoints directly (not the settings save).
 */

import { useState, useEffect } from '@wordpress/element';
import { TextControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { apiGet, apiPost, apiDelete } from '../api';
import Notice from '../components/Notice';

const weekdays = window.ordddAdminData?.weekdays ?? {};

export default function DisabledSlotsTable() {
	const [ blockedSlots, setBlockedSlots ] = useState( [] );
	const [ availableSlots, setAvailableSlots ] = useState( [] );
	const [ adding, setAdding ] = useState( false );
	const [ newEntry, setNewEntry ] = useState( {
		block_time_slot_for: 'weekdays',
		delivery_days: '',
		timeslot: '',
	} );
	const [ notice, setNotice ] = useState( null );

	const fetchData = () => {
		apiGet( '/settings/block-time-slot' )
			.then( ( data ) => {
				setBlockedSlots( data.blocked_slots ?? [] );
				setAvailableSlots( data.available_slots ?? [] );
			} )
			.catch( () => {} );
	};

	useEffect( () => {
		fetchData();
	}, [] );

	const weekdayOptions = Object.entries( weekdays ).map( ( [ value, label ] ) => ( {
		value,
		label,
	} ) );

	const handleAdd = () => {
		if ( ! newEntry.delivery_days || ! newEntry.timeslot ) {
			setNotice( {
				type: 'error',
				message: __( 'Day and time slot are required.', 'order-delivery-date' ),
			} );
			return;
		}

		apiPost( '/settings/block-time-slot', newEntry )
			.then( ( response ) => {
				setBlockedSlots( response.data ?? [] );
				setAdding( false );
				setNewEntry( { block_time_slot_for: 'weekdays', delivery_days: '', timeslot: '' } );
				setNotice( { type: 'success', message: __( 'Time slot blocked.', 'order-delivery-date' ) } );
			} )
			.catch( ( err ) =>
				setNotice( {
					type: 'error',
					message: err?.message ?? __( 'Failed to block time slot.', 'order-delivery-date' ),
				} )
			);
	};

	const handleDelete = ( entry ) => {
		apiDelete( '/settings/block-time-slot', {
			delivery_days: entry.delivery_days,
			timeslot: entry.time_slot,
		} )
			.then( ( response ) => {
				setBlockedSlots( response.data ?? [] );
				setNotice( { type: 'success', message: __( 'Time slot unblocked.', 'order-delivery-date' ) } );
			} )
			.catch( ( err ) =>
				setNotice( {
					type: 'error',
					message: err?.message ?? __( 'Failed to unblock.', 'order-delivery-date' ),
				} )
			);
	};

	return (
		<div className="orddd-lite-table-wrap">
			{ notice && (
				<Notice
					type={ notice.type }
					message={ notice.message }
					onDismiss={ () => setNotice( null ) }
				/>
			) }
			<table className="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>{ __( 'For', 'order-delivery-date' ) }</th>
						<th>{ __( 'Day / Date', 'order-delivery-date' ) }</th>
						<th>{ __( 'Time Slot', 'order-delivery-date' ) }</th>
						<th>{ __( 'Actions', 'order-delivery-date' ) }</th>
					</tr>
				</thead>
				<tbody>
					{ blockedSlots.length === 0 && (
						<tr>
							<td colSpan="4">
								{ __( 'No blocked time slots.', 'order-delivery-date' ) }
							</td>
						</tr>
					) }
					{ blockedSlots.map( ( entry, i ) => (
						<tr key={ i }>
							<td>{ entry.block_time_slot_for }</td>
							<td>{ weekdays[ entry.delivery_days ] ?? entry.delivery_days }</td>
							<td>{ entry.time_slot }</td>
							<td>
								<button
									type="button"
									className="button button-small button-link-delete"
									onClick={ () => handleDelete( entry ) }
								>
									{ __( 'Delete', 'order-delivery-date' ) }
								</button>
							</td>
						</tr>
					) ) }
				</tbody>
			</table>

			{ adding ? (
				<div className="orddd-lite-add-row">
					<h4>{ __( 'Block Time Slot', 'order-delivery-date' ) }</h4>
					<SelectControl
						label={ __( 'Day', 'order-delivery-date' ) }
						value={ newEntry.delivery_days }
						options={ [ { value: '', label: __( '— Select —', 'order-delivery-date' ) }, ...weekdayOptions ] }
						onChange={ ( v ) => setNewEntry( { ...newEntry, delivery_days: v } ) }
					/>
					<SelectControl
						label={ __( 'Time Slot', 'order-delivery-date' ) }
						value={ newEntry.timeslot }
						options={ [
							{ value: '', label: __( '— Select —', 'order-delivery-date' ) },
							...availableSlots.map( ( s ) => ( { value: s, label: s } ) ),
						] }
						onChange={ ( v ) => setNewEntry( { ...newEntry, timeslot: v } ) }
					/>
					<div className="orddd-lite-add-row__actions">
						<button type="button" className="button button-primary" onClick={ handleAdd }>
							{ __( 'Add', 'order-delivery-date' ) }
						</button>
						<button type="button" className="button" onClick={ () => setAdding( false ) }>
							{ __( 'Cancel', 'order-delivery-date' ) }
						</button>
					</div>
				</div>
			) : (
				<button type="button" className="button" onClick={ () => setAdding( true ) }>
					{ __( '+ Block Time Slot', 'order-delivery-date' ) }
				</button>
			) }
		</div>
	);
}
