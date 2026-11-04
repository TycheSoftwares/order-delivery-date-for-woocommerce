/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { ToggleControl, Notice, Spinner, Button } from '@wordpress/components';
import { Icon, chevronDown, chevronUp } from '@wordpress/icons';
import { __, sprintf } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { DatePickerField } from '../../components/SettingsForm';

// ── Helpers ───────────────────────────────────────────────────────────────────

const formatDate = ( dateStr ) => {
	if ( ! dateStr ) return '';
	const d = new Date( dateStr + 'T00:00:00' );
	return d.toLocaleDateString( 'en-GB', { day: '2-digit', month: 'short', year: 'numeric' } );
};


const EMPTY_ROW    = { holiday_name: '', holiday_date: '', holiday_type: 'current' };
const EMPTY_NEWROW = { holiday_name: '', holiday_date_from: '', holiday_date_to: '', holiday_type: 'current' };

// ── RepeatBadge ───────────────────────────────────────────────────────────────

const RepeatBadge = ( { type } ) => (
	<span className={ `hol-repeat-badge hol-repeat-badge--${ type === 'recurring' ? 'recurring' : 'current' }` }>
		{ type === 'recurring' ? __( 'Recurring', 'order-delivery-date' ) : __( 'Current Year', 'order-delivery-date' ) }
	</span>
);

// ── HolidayRow (display) ──────────────────────────────────────────────────────

const HolidayRow = ( { row, selected, onSelect, onEdit, onDelete, isDeleting } ) => (
	<tr>
		<td className="hol-td-check">
			<input
				type="checkbox"
				checked={ selected }
				onChange={ ( e ) => onSelect( e.target.checked ) }
			/>
		</td>
		<td>{ row.holiday_name }</td>
		<td>{ formatDate( row.holiday_date ) }</td>
		<td><RepeatBadge type={ row.holiday_type } /></td>
		<td className="hol-td-actions">
			<Button variant="link"  size={'default'} onClick={ onEdit } style={ { marginRight: '5px', height: '36px' }}>{ __( 'Edit', 'order-delivery-date' ) }</Button>
			<Button variant="link" size={'default'} isDestructive isBusy={ isDeleting } onClick={ onDelete }>{ __( 'Delete', 'order-delivery-date' ) }</Button>
		</td>
	</tr>
);

// ── EditRow ───────────────────────────────────────────────────────────────────

const EditRow = ( { data, onChange, onSave, onCancel, isSaving } ) => (
	<tr className="hol-edit-row">
		<td className="hol-td-check" />
		<td>
			<input
				type="text"
				className="hol-input"
				value={ data.holiday_name }
				onChange={ ( e ) => onChange( { ...data, holiday_name: e.target.value } ) }
			/>
		</td>
		<td>
			<DatePickerField
				value={ data.holiday_date }
				onChange={ ( date ) => onChange( { ...data, holiday_date: date } ) }
				placeholder={ __( 'Select date', 'order-delivery-date' ) }
			/>
		</td>
		<td>
			<select
				className="hol-select"
				value={ data.holiday_type }
				onChange={ ( e ) => onChange( { ...data, holiday_type: e.target.value } ) }
			>
				<option value="current">{ __( 'Current Year', 'order-delivery-date' ) }</option>
				<option value="recurring">{ __( 'Every Year', 'order-delivery-date' ) }</option>
			</select>
		</td>
		<td className="hol-td-actions">
			<Button variant="link" isBusy={ isSaving } onClick={ onSave }>{ __( 'Update', 'order-delivery-date' ) }</Button>
			<Button variant="link" onClick={ onCancel }>{ __( 'Cancel', 'order-delivery-date' ) }</Button>
		</td>
	</tr>
);

// ── ScheduleCard ──────────────────────────────────────────────────────────────

const ScheduleCard = ( { schedule } ) => {
	const isGlobal    = String( schedule.delivery_schedule_id ) === '0';
	const scheduleId  = schedule.delivery_schedule_id;
	const apiBase     = isGlobal ? '/orddd/v1/holidays' : `/orddd/v1/holidays/${ scheduleId }`;
	const type       = schedule.schedule_type ?? 'delivery'; // 'delivery' | 'pickup'

	const [ isOpen,       setIsOpen       ] = useState( true );
	const [ rows,         setRows         ] = useState( schedule.holidays ?? [] );
	const [ newRow,       setNewRow       ] = useState( EMPTY_NEWROW );
	const [ editingDate,  setEditingDate  ] = useState( null ); // original date of row being edited
	const [ editRow,      setEditRow      ] = useState( EMPTY_ROW );
	const [ selected,     setSelected     ] = useState( {} ); // { [date]: boolean }
	const [ bulkAction,   setBulkAction   ] = useState( '' );
	const [ showAddRow,   setShowAddRow   ] = useState( false );
	const [ savingNew,    setSavingNew    ] = useState( false );
	const [ savingDate,   setSavingDate   ] = useState( null );
	const [ deletingDate, setDeletingDate ] = useState( null );
	const [ enableGlobal, setEnableGlobal ] = useState( !! schedule.enable_global_holidays );
	const [ globalSaving, setGlobalSaving ] = useState( false );

	const holidayCount = rows.length;
	const selectedDates  = Object.keys( selected ).filter( ( d ) => selected[ d ] );
	const allSelected    = rows.length > 0 && selectedDates.length === rows.length;
	const someSelected   = selectedDates.length > 0 && selectedDates.length < rows.length;

	// ── Toggle global holidays ─────────────────────────────────────────────────

	const toggleGlobal = async ( val ) => {
		setEnableGlobal( val );
		setGlobalSaving( true );
		try {
			await apiFetch( {
				path:   `/orddd/v1/holidays/${ scheduleId }/global`,
				method: 'POST',
				data:   { enable_global_holidays: val },
			} );
		} catch ( e ) {}
		finally { setGlobalSaving( false ); }
	};

	// ── Add new holiday ────────────────────────────────────────────────────────

	const saveNew = async () => {
		const { holiday_name, holiday_date_from, holiday_date_to, holiday_type } = newRow;
		if ( ! holiday_name || ! holiday_date_from || ! holiday_date_to ) return;
		setSavingNew( true );
		try {
			const response = await apiFetch( {
				path:   apiBase,
				method: 'POST',
				data:   { holiday_name, holiday_date_from, holiday_date_to, holiday_type },
			} );
			setRows( response?.data ?? [] );
			setNewRow( EMPTY_NEWROW );
			setShowAddRow( false );
		} catch ( e ) {}
		finally { setSavingNew( false ); }
	};

	// ── Update holiday (delete + add) ──────────────────────────────────────────

	const saveEdit = async ( originalDate ) => {
		setSavingDate( originalDate );
		try {
			// Delete old entry
			await apiFetch( {
				path:   `${ apiBase }?holiday_date=${ encodeURIComponent( originalDate ) }`,
				method: 'DELETE',
			} );
			// Add updated entry
			await apiFetch( {
				path:   apiBase,
				method: 'POST',
				data:   editRow,
			} );
			setRows( ( prev ) => prev.map( ( r ) =>
				r.holiday_date === originalDate ? { ...editRow } : r
			) );
			setEditingDate( null );
			setEditRow( EMPTY_ROW );
		} catch ( e ) {}
		finally { setSavingDate( null ); }
	};

	// ── Delete holiday ─────────────────────────────────────────────────────────

	const deleteHoliday = async ( date ) => {
		if ( ! window.confirm( __( 'Delete this holiday?', 'order-delivery-date' ) ) ) return;
		setDeletingDate( date );
		try {
			await apiFetch( {
				path:   `${ apiBase }?holiday_date=${ encodeURIComponent( date ) }`,
				method: 'DELETE',
			} );
			setRows( ( prev ) => prev.filter( ( r ) => r.holiday_date !== date ) );
			setSelected( ( prev ) => { const n = { ...prev }; delete n[ date ]; return n; } );
		} catch ( e ) {}
		finally { setDeletingDate( null ); }
	};

	// ── Bulk action ────────────────────────────────────────────────────────────

	const applyBulk = async () => {
		if ( bulkAction !== 'delete' || ! selectedDates.length ) return;
		for ( const date of selectedDates ) {
			await apiFetch( {
				path:   `${ apiBase }?holiday_date=${ encodeURIComponent( date ) }`,
				method: 'DELETE',
			} );
		}
		setRows( ( prev ) => prev.filter( ( r ) => ! selected[ r.holiday_date ] ) );
		setSelected( {} );
		setBulkAction( '' );
	};

	// ── Select helpers ────────────────────────────────────────────────────────

	const toggleAll = ( checked ) => {
		const next = {};
		if ( checked ) rows.forEach( ( r ) => { next[ r.holiday_date ] = true; } );
		setSelected( next );
	};

	const toggleOne = ( date, checked ) => {
		setSelected( ( prev ) => ( { ...prev, [ date ]: checked } ) );
	};

	// ── Start editing a row ───────────────────────────────────────────────────

	const startEdit = ( row ) => {
		setEditingDate( row.holiday_date );
		setEditRow( {
			holiday_name: row.holiday_name,
			holiday_date: row.holiday_date,
			holiday_type: row.holiday_type,
		} );
	};

	// ── Render ────────────────────────────────────────────────────────────────

	return (
		<div className="hol-card">

			{ /* ── Card header ── */ }
			<div
				className="hol-card-header"
				onClick={ () => setIsOpen( ( o ) => ! o ) }
				style={ { cursor: 'pointer' } }
			>
				{ isGlobal ? (
					<>
						<div className="hol-card-header-left">
							{/* <div className="hol-globe-avatar">🌐</div> */}
							<div>
								<div className="hol-card-title">{ __( 'Global Holidays', 'order-delivery-date' ) }</div>
								{/* <div className="hol-card-subtitle">
									{ __( 'Applied to all schedules that have "Use Global Holidays" enabled', 'order-delivery-date' ) }
								</div> */}
							</div>
						</div>
						<div className="hol-card-header-right">
							{ holidayCount > 0 && (
								<span className="hol-count-badge">
									{ holidayCount === 1 ? __( '1 holiday', 'order-delivery-date' ) : sprintf( __( '%d holidays', 'order-delivery-date' ), holidayCount ) }
								</span>
							) }
							<Icon icon={ isOpen ? chevronUp : chevronDown } />
						</div>
					</>
				) : (
					<>
						<div className="hol-card-header-left">
							{/* <span className={ `hol-type-badge hol-type-badge--${ type }` }>
								<span className="hol-type-dot" />
								{ type === 'pickup' ? __( 'Pickup', 'order-delivery-date' ) : __( 'Delivery', 'order-delivery-date' ) }
							</span> */}
							<span
								className="hol-card-title"
								title={ schedule.title_full || undefined }
							>{ schedule.title || sprintf( __( 'Schedule ID: %d', 'order-delivery-date' ), scheduleId ) }</span>
							{/* <span className="hol-shipping-label">{ schedule.subtitle ?? __( 'Shipping Method', 'order-delivery-date' ) }</span> */}
							{ holidayCount > 0 && (
								<span className="hol-count-badge">
									{ holidayCount === 1 ? __( '1 holiday', 'order-delivery-date' ) : sprintf( __( '%d holidays', 'order-delivery-date' ), holidayCount ) }
								</span>
							) }
							{ schedule.has_global && (
								<span className="hol-global-badge">🌐 { __( 'Global', 'order-delivery-date' ) }</span>
							) }
						</div>
						<div
							className="hol-card-header-right"
							onClick={ ( e ) => e.stopPropagation() }
						>
							<div className="hol-global-toggle">
								<span className="hol-global-toggle-label">{ __( 'Use Global Holidays', 'order-delivery-date' ) }</span>
								<ToggleControl
									checked={ enableGlobal }
									onChange={ toggleGlobal }
									disabled={ globalSaving }
									__nextHasNoMarginBottom
								/>
							</div>
							<button
								type="button"
								className="hol-collapse-btn"
								onClick={ ( e ) => { e.stopPropagation(); setIsOpen( ( o ) => ! o ); } }
							>
								<Icon icon={ isOpen ? chevronUp : chevronDown } />
							</button>
						</div>
					</>
				) }
			</div>

			{ /* ── Card body ── */ }
			{ isOpen && (
				<div className="hol-card-body" style={ { position: 'relative' } }>
					{ ( savingNew || savingDate || deletingDate ) && (
						<div style={ { position: 'absolute', inset: 0, background: 'rgba(255,255,255,0.6)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 10 } }>
							<Spinner />
						</div>
					) }
					<table className="hol-table">
						<thead>
							<tr>
								<th className="hol-th-check">
									<input
										type="checkbox"
										checked={ allSelected }
										ref={ ( el ) => { if ( el ) el.indeterminate = someSelected; } }
										onChange={ ( e ) => toggleAll( e.target.checked ) }
									/>
								</th>
								<th>{ __( 'Name', 'order-delivery-date' ) }</th>
								<th className="hol-th-date">{ __( 'Date', 'order-delivery-date' ) }</th>
								<th>{ __( 'Repeat Type', 'order-delivery-date' ) }</th>
								<th className="hol-th-actions">{ __( 'Actions', 'order-delivery-date' ) }</th>
							</tr>
						</thead>
						<tbody>
							{ rows.map( ( row ) =>
								editingDate === row.holiday_date ? (
									<EditRow
										key={ row.holiday_date }
										data={ editRow }
										onChange={ setEditRow }
										onSave={ () => saveEdit( row.holiday_date ) }
										onCancel={ () => { setEditingDate( null ); setEditRow( EMPTY_ROW ); } }
										isSaving={ savingDate === row.holiday_date }
									/>
								) : (
									<HolidayRow
										key={ row.holiday_date }
										row={ row }
										selected={ !! selected[ row.holiday_date ] }
										onSelect={ ( checked ) => toggleOne( row.holiday_date, checked ) }
										onEdit={ () => startEdit( row ) }
										onDelete={ () => deleteHoliday( row.holiday_date ) }
										isDeleting={ deletingDate === row.holiday_date }
									/>
								)
							) }

							{ /* ── Inline add row ── */ }
							{ showAddRow && (
								<tr className="hol-add-row">
									<td className="hol-add-icon">+</td>
									<td>
										<input
											type="text"
											className="hol-input"
											placeholder={ __( 'New holiday name...', 'order-delivery-date' ) }
											value={ newRow.holiday_name }
											onChange={ ( e ) => setNewRow( ( r ) => ( { ...r, holiday_name: e.target.value } ) ) }
											onKeyDown={ ( e ) => { if ( e.key === 'Enter' ) saveNew(); } }
										/>
									</td>
									<td>
										<div className="hol-date-range">
											<DatePickerField
												value={ newRow.holiday_date_from }
												onChange={ ( date ) => setNewRow( ( r ) => ( {
												...r,
												holiday_date_from: date,
												holiday_date_to: ( ! r.holiday_date_to || r.holiday_date_to === r.holiday_date_from ) ? date : r.holiday_date_to,
											} ) ) }
												placeholder={ __( 'From date', 'order-delivery-date' ) }
											/>
											<span className="hol-date-range-sep">{ __( 'To', 'order-delivery-date' ) }</span>
											<DatePickerField
												value={ newRow.holiday_date_to }
												onChange={ ( date ) => setNewRow( ( r ) => ( { ...r, holiday_date_to: date } ) ) }
												placeholder={ __( 'To date', 'order-delivery-date' ) }
											/>
										</div>
									</td>
									<td>
										<select
											className="hol-select"
											value={ newRow.holiday_type }
											onChange={ ( e ) => setNewRow( ( r ) => ( { ...r, holiday_type: e.target.value } ) ) }
										>
											<option value="current">{ __( 'Current Year', 'order-delivery-date' ) }</option>
											<option value="recurring">{ __( 'Every Year', 'order-delivery-date' ) }</option>
										</select>
									</td>
									<td className="hol-td-actions">
										<Button variant="primary" isMedium isBusy={ savingNew } onClick={ saveNew }>
											{ __( 'Save', 'order-delivery-date' ) }
										</Button>
									</td>
								</tr>
							) }
						</tbody>
					</table>

					<div className="hol-add-more-row">
						{ showAddRow ? (
							<Button
								variant="link"
								className="odt-cancel-link"
								onClick={ () => { setShowAddRow( false ); setNewRow( EMPTY_NEWROW ); } }
							>
								{ __( '✕ Cancel', 'order-delivery-date' ) }
							</Button>
						) : (
							<Button
								variant="link"
								className="odt-add-more-link"
								onClick={ () => setShowAddRow( true ) }
							>
								<span className="odt-add-more-plus" aria-hidden="true">+</span>
								<span className="odt-add-more-label">{ __( 'Add More', 'order-delivery-date' ) }</span>
							</Button>
						) }
					</div>

					{ rows.length > 0 && (
						<div className="hol-bulk-row">
							<select
								className="hol-select"
								value={ bulkAction }
								onChange={ ( e ) => setBulkAction( e.target.value ) }
							>
								<option value="">{ __( 'Bulk Action', 'order-delivery-date' ) }</option>
								<option value="delete">{ __( 'Delete', 'order-delivery-date' ) }</option>
							</select>
							<Button
								variant="secondary"
								disabled={ ! bulkAction || ! selectedDates.length }
								onClick={ applyBulk }
							>
								{ __( 'Apply', 'order-delivery-date' ) }
							</Button>
						</div>
					) }
				</div>
			) }
		</div>
	);
};

// ── Holidays ──────────────────────────────────────────────────────────────────

const Holidays = () => {
	const [ loading,        setLoading        ] = useState( true );
	const [ globalSchedule, setGlobalSchedule ] = useState( null );
	const [ notice,         setNotice         ] = useState( null );

	useEffect( () => {
		apiFetch( { path: '/orddd/v1/holidays' } )
			.then( ( res ) => {
				const holidays = res?.data ?? ( Array.isArray( res ) ? res : [] );
				setGlobalSchedule( {
					delivery_schedule_id:   0,
					holidays,
					enable_global_holidays: false,
				} );
			} )
			.catch( () =>
				setNotice( { type: 'error', message: __( 'Could not load holidays. Please refresh and try again.', 'order-delivery-date' ) } )
			)
			.finally( () => setLoading( false ) );
	}, [] );

	if ( loading ) {
		return <div className="bd-loading"><Spinner /></div>;
	}

	return (
		<div className="orddd-page-wrap">
			{ notice && (
				<Notice status={ notice.type } isDismissible onRemove={ () => setNotice( null ) }>
					{ notice.message }
				</Notice>
			) }

			<div className="hol-page-header">
				<div className="hol-page-header-text">
					<h1 className="hol-title">{ __( 'Holidays', 'order-delivery-date' ) }</h1>
					<p className="hol-subtitle">
						{ __( 'Dates added here will be blocked and unavailable for delivery selection on the checkout page.', 'order-delivery-date' ) }
					</p>
				</div>
			</div>

			<div className="hol-cards">
				{ globalSchedule && (
					<ScheduleCard
						key={ 0 }
						schedule={ globalSchedule }
					/>
				) }
			</div>
		</div>
	);
};

export default Holidays;
