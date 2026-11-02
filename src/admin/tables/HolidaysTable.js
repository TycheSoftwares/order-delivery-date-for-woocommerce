/**
 * HolidaysTable – CRUD table for orddd_lite_holidays.
 * Uses Pro's hol-* CSS class naming.
 */

import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { apiGet, apiPost, apiDelete } from '../api';
import Notice from '../components/Notice';

export default function HolidaysTable() {
	const [ holidays, setHolidays ]     = useState( [] );
	const [ collapsed, setCollapsed ]   = useState( false );
	const [ notice, setNotice ]         = useState( null );

	// New-row form state
	const [ newName, setNewName ]       = useState( '' );
	const [ newDate, setNewDate ]       = useState( '' );
	const [ newType, setNewType ]       = useState( 'current' );
	const [ isAdding, setIsAdding ]     = useState( false );

	const fetchHolidays = () => {
		apiGet( '/holidays' ).then( setHolidays ).catch( () => {} );
	};

	useEffect( () => {
		fetchHolidays();
	}, [] );

	const handleAdd = () => {
		if ( ! newName.trim() || ! newDate ) {
			setNotice( {
				type: 'error',
				message: __( 'Holiday name and date are required.', 'order-delivery-date' ),
			} );
			return;
		}
		setIsAdding( true );
		apiPost( '/holidays', {
			holiday_name: newName.trim(),
			holiday_date: newDate,
			holiday_type: newType,
		} )
			.then( ( response ) => {
				setHolidays( response.data );
				setNewName( '' );
				setNewDate( '' );
				setNewType( 'current' );
				setNotice( {
					type: 'success',
					message: __( 'Holiday added.', 'order-delivery-date' ),
				} );
			} )
			.catch( ( err ) =>
				setNotice( {
					type: 'error',
					message: err?.message ?? __( 'Failed to add holiday.', 'order-delivery-date' ),
				} )
			)
			.finally( () => setIsAdding( false ) );
	};

	const handleDelete = ( holidayDate ) => {
		apiDelete( '/holidays', { holiday_date: holidayDate } )
			.then( ( response ) => {
				setHolidays( response.data );
				setNotice( {
					type: 'success',
					message: __( 'Holiday removed.', 'order-delivery-date' ),
				} );
			} )
			.catch( ( err ) =>
				setNotice( {
					type: 'error',
					message: err?.message ?? __( 'Failed to delete holiday.', 'order-delivery-date' ),
				} )
			);
	};

	const countLabel = holidays.length === 1
		? __( '1 holiday', 'order-delivery-date' )
		: `${ holidays.length } ${ __( 'holidays', 'order-delivery-date' ) }`;

	return (
		<div>
			{ notice && (
				<Notice
					type={ notice.type }
					message={ notice.message }
					onDismiss={ () => setNotice( null ) }
				/>
			) }

			<div className="hol-cards">
				<div className="hol-card">

					{ /* ── Card header ── */ }
					<div className="hol-card-header">
						<div className="hol-card-header-left">
							<div className="hol-globe-avatar">🌐</div>
							<div>
								<div className="hol-card-title">
									{ __( 'Delivery Holidays', 'order-delivery-date' ) }
								</div>
								<div className="hol-card-subtitle">
									{ __( 'Dates when delivery is unavailable', 'order-delivery-date' ) }
								</div>
							</div>
							<span className="hol-type-badge hol-type-badge--delivery">
								{ __( 'Delivery', 'order-delivery-date' ) }
							</span>
							<span className="hol-count-badge">{ countLabel }</span>
						</div>
						<div className="hol-card-header-right">
							<button
								type="button"
								className="hol-collapse-btn"
								onClick={ () => setCollapsed( ( c ) => ! c ) }
								aria-expanded={ ! collapsed }
								title={ collapsed
									? __( 'Expand', 'order-delivery-date' )
									: __( 'Collapse', 'order-delivery-date' ) }
							>
								{ collapsed ? '▼' : '▲' }
							</button>
						</div>
					</div>

					{ /* ── Card body ── */ }
					{ ! collapsed && (
						<div className="hol-card-body">
							<table className="hol-table">
								<thead>
									<tr>
										<th className="hol-td-check"></th>
										<th>{ __( 'Holiday Name', 'order-delivery-date' ) }</th>
										<th className="hol-th-date">{ __( 'Date', 'order-delivery-date' ) }</th>
										<th>{ __( 'Type', 'order-delivery-date' ) }</th>
										<th className="hol-th-actions">{ __( 'Actions', 'order-delivery-date' ) }</th>
									</tr>
								</thead>
								<tbody>
									{ holidays.length === 0 && (
										<tr>
											<td colSpan="5" style={ { padding: '20px 16px', color: '#757575', fontStyle: 'italic' } }>
												{ __( 'No holidays added yet.', 'order-delivery-date' ) }
											</td>
										</tr>
									) }
									{ holidays.map( ( h, i ) => (
										<tr key={ i }>
											<td className="hol-td-check">
												<input type="checkbox" style={ { margin: 0 } } />
											</td>
											<td>{ h.holiday_name }</td>
											<td>{ h.holiday_date }</td>
											<td>
												<span className={
													`hol-repeat-badge ${ h.holiday_type === 'recurring'
														? 'hol-repeat-badge--recurring'
														: 'hol-repeat-badge--current' }`
												}>
													{ h.holiday_type === 'recurring'
														? __( 'Recurring', 'order-delivery-date' )
														: __( 'One-time', 'order-delivery-date' ) }
												</span>
											</td>
											<td>
												<div className="hol-td-actions">
													<button
														type="button"
														className="es-link-btn es-link-btn--delete"
														onClick={ () => handleDelete( h.holiday_date ) }
													>
														{ __( 'Delete', 'order-delivery-date' ) }
													</button>
												</div>
											</td>
										</tr>
									) ) }

									{ /* ── Inline add row ── */ }
									<tr>
										<td className="hol-td-check"></td>
										<td>
											<input
												type="text"
												className="hol-input"
												placeholder={ __( 'Holiday name', 'order-delivery-date' ) }
												value={ newName }
												onChange={ ( e ) => setNewName( e.target.value ) }
											/>
										</td>
										<td>
											<input
												type="date"
												className="hol-input hol-date-input"
												value={ newDate }
												onChange={ ( e ) => setNewDate( e.target.value ) }
											/>
										</td>
										<td>
											<select
												className="hol-select"
												value={ newType }
												onChange={ ( e ) => setNewType( e.target.value ) }
											>
												<option value="current">{ __( 'One-time', 'order-delivery-date' ) }</option>
												<option value="recurring">{ __( 'Recurring', 'order-delivery-date' ) }</option>
											</select>
										</td>
										<td>
											<div className="hol-td-actions">
												<button
													type="button"
													className="es-link-btn es-link-btn--edit"
													onClick={ handleAdd }
													disabled={ isAdding }
												>
													{ isAdding
														? __( 'Adding…', 'order-delivery-date' )
														: __( 'Add', 'order-delivery-date' ) }
												</button>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					) }
				</div>
			</div>
		</div>
	);
}
