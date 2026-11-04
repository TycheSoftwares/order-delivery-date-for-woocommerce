/**
 * DataTable
 *
 * A reusable table component built on @wordpress/components Card primitives.
 *
 * Usage:
 *
 *   <DataTable
 *     columns={ [
 *       { key: 'name',  label: 'Name' },
 *       { key: 'email', label: 'Email' },
 *     ] }
 *     rows={ data }
 *     rowKey="id"
 *     renderCell={ ( row, colKey ) => row[ colKey ] }   // optional custom cell renderer
 *     renderActions={ ( row ) => <Button>Delete</Button> }
 *     loading={ loading }
 *     showForm={ showForm }
 *     onToggleForm={ () => setShowForm( v => !v ) }
 *     renderForm={ <MyInlineForm /> }
 *     bulkActions={ [ { value: 'delete', label: 'Delete' } ] }
 *     onBulkApply={ ( actionValue, selectedRows ) => {} }
 *     emptyMessage="No records found."
 *   />
 *
 * Props:
 *   columns        {Array<{ key: string, label: string }>}  Column definitions.
 *   rows           {Array<Object>}                          Row data.
 *   rowKey         {string|Function}                        Unique key per row — field name or (row)=>key.
 *   renderCell     {Function} (row, colKey) => ReactNode    Optional. Falls back to row[colKey].
 *   renderActions  {Function} (row) => ReactNode            Actions cell content. Omit to hide column.
 *   loading        {boolean}                                Show spinner instead of rows.
 *   showForm       {boolean}                                Controlled: whether the inline form is visible.
 *   onToggleForm   {Function}                               Called when Add More / Cancel is clicked.
 *   renderForm     {ReactNode}                              Inline form content shown when showForm=true.
 *   addMoreLabel   {string}                                 Link text (default "Add More").
 *   bulkActions    {Array<{ value, label }>}                Options for the Bulk Action dropdown.
 *   onBulkApply    {Function} (actionValue, rows) => void   Called when Apply is clicked.
 *   emptyMessage   {string}                                 Empty-state text.
 */

import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import {
	Card,
	CardBody,
	CardDivider,
	Button,
	SelectControl,
	Spinner,
	Tooltip,
} from '@wordpress/components';


const getRowKey = ( row, rowKey ) =>
	typeof rowKey === 'function' ? rowKey( row ) : row[ rowKey ];

const DataTable = ( {
	columns        = [],
	rows           = [],
	rowKey         = 'id',
	renderCell,
	renderActions,
	rowClassName,
	loading        = false,
	showForm       = false,
	onToggleForm,
	renderForm,
	addMoreLabel   = __( 'Add More', 'order-delivery-date' ),
	bulkActions    = [],
	onBulkApply,
	emptyMessage   = __( 'No records found.', 'order-delivery-date' ),
	showCheckboxes = true,
} ) => {
	const [ selected,   setSelected   ] = useState( [] );
	const [ bulkAction, setBulkAction ] = useState( '' );

	const hasActions = typeof renderActions === 'function';
	const allChecked = rows.length > 0 && selected.length === rows.length;
	const colTemplate = `${ showCheckboxes ? '40px ' : '' }repeat(${ columns.length }, 1fr)${ hasActions ? ' 120px' : '' }`;

	const toggleAll = () =>
		setSelected( allChecked ? [] : rows.map( ( r ) => getRowKey( r, rowKey ) ) );

	const toggleOne = ( key ) =>
		setSelected( ( prev ) =>
			prev.includes( key ) ? prev.filter( ( k ) => k !== key ) : [ ...prev, key ]
		);

	const handleApply = () => {
		if ( ! bulkAction || ! onBulkApply ) return;
		const selectedRows = rows.filter( ( r ) => selected.includes( getRowKey( r, rowKey ) ) );
		onBulkApply( bulkAction, selectedRows );
		setSelected( [] );
		setBulkAction( '' );
	};

	return (
		<div className="odt-wrap">

			{ /* ── Card container (border, radius, shadow from WP) ── */ }
			<Card className="odt-card" size="none">

				{ /* ── Table header ── */ }
				<div className="odt-header" style={ { gridTemplateColumns: colTemplate } }>
					{ showCheckboxes && (
						<div className="odt-cell">
							<input
								type="checkbox"
								className="odt-checkbox"
								checked={ allChecked }
								onChange={ toggleAll }
								aria-label={ __( 'Select all rows', 'order-delivery-date' ) }
							/>
						</div>
					) }
					{ columns.map( ( col ) => (
						<div key={ col.key } className="odt-cell odt-cell--heading">
							{ col.label }
							{ col.tooltip && (
								<Tooltip text={ col.tooltip }>
									<span className="odt-col-tooltip-icon" aria-label={ col.tooltip }>
										&#9432;
									</span>
								</Tooltip>
							) }
						</div>
					) ) }
					{ hasActions && (
						<div className="odt-cell odt-cell--heading">{ __( 'Actions', 'order-delivery-date' ) }</div>
					) }
				</div>

				{ /* ── Body ── */ }
				{ loading ? (
					<CardBody className="odt-loading">
						<Spinner />
					</CardBody>
				) : (
					<>
						{ rows.length === 0 && ! showForm && (
							<CardBody className="odt-empty">
								{ emptyMessage }
							</CardBody>
						) }

						{ rows.map( ( row ) => {
							const key     = getRowKey( row, rowKey );
							const checked = selected.includes( key );

							return (
								<div key={ key }>
									<CardDivider />
									<div
										className={ `odt-row${ checked ? ' odt-row--selected' : '' }${ rowClassName ? ' ' + rowClassName( row ) : '' }` }
										style={ { gridTemplateColumns: colTemplate } }
									>
										{ showCheckboxes && (
										<div className="odt-cell">
											<input
												type="checkbox"
												className="odt-checkbox"
												checked={ checked }
												onChange={ () => toggleOne( key ) }
												aria-label={ __( 'Select row', 'order-delivery-date' ) }
											/>
										</div>
									) }
										{ columns.map( ( col ) => (
											<div key={ col.key } className="odt-cell">
												{ renderCell
													? renderCell( row, col.key )
													: ( row[ col.key ] ?? '—' )
												}
											</div>
										) ) }
										{ hasActions && (
											<div className="odt-cell">
												{ renderActions( row ) }
											</div>
										) }
									</div>
								</div>
							);
						} ) }

						{ /* ── Inline form slot ── */ }
						{ showForm && (
							<>
								<CardDivider />
								<CardBody size="none" className="odt-form-slot">
									{ renderForm }
								</CardBody>
							</>
						) }

						{ /* ── Add More / Cancel link — only when a toggle handler is provided ── */ }
						{ onToggleForm && (
							<>
								<CardDivider />
								<CardBody className="odt-add-more-row" size="small">
									{ showForm ? (
										<Button
											variant="link"
											className="odt-cancel-link"
											onClick={ onToggleForm }
										>
											{ __( '✕ Cancel', 'order-delivery-date' ) }
										</Button>
									) : (
										<Button
											variant="link"
											className="odt-add-more-link"
											onClick={ onToggleForm }
										>
											<span className="odt-add-more-plus" aria-hidden="true">+</span>
											<span className="odt-add-more-label">{ addMoreLabel }</span>
										</Button>
									) }
								</CardBody>
							</>
						) }
					</>
				) }
			</Card>

			{ /* ── Footer: Bulk Action ── */ }
			{ bulkActions.length > 0 && (
				<div className="odt-footer">
					<SelectControl
						className="odt-bulk-select"
						value={ bulkAction }
						options={ [
							{ value: '', label: __( 'Bulk Action', 'order-delivery-date' ) },
							...bulkActions,
						] }
						onChange={ ( val ) => setBulkAction( val ) }
						__nextHasNoMarginBottom
					/>
					<Button
						variant="secondary"
						className="odt-apply-btn"
						onClick={ handleApply }
					>
						{ __( 'Apply', 'order-delivery-date' ) }
					</Button>
				</div>
			) }
		</div>
	);
};

export default DataTable;
