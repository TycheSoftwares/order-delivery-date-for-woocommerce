import { __ } from '@wordpress/i18n';

const upgradeUrl =
	window.ordddAdminData?.upgradeUrl ??
	'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/';

/** Inline radio-button group (WP RadioControl stacks; this keeps them horizontal). */
const InlineRadios = ( { name, options, value, onChange } ) => (
	<div style={ { display: 'flex', gap: '20px', flexWrap: 'wrap' } }>
		{ options.map( ( opt ) => (
			<label
				key={ opt.value }
				style={ {
					display:    'flex',
					alignItems: 'center',
					gap:        '6px',
					cursor:     opt.locked ? 'default' : 'pointer',
					fontSize:   '13px',
					opacity:    opt.locked ? 0.75 : 1,
				} }
			>
				<input
					type="radio"
					name={ name }
					value={ opt.value }
					checked={ value === opt.value }
					onChange={ () => ! opt.locked && onChange( opt.value ) }
					disabled={ opt.locked }
				/>
				{ opt.label }
				{ opt.locked && (
					<>
						<span className="orddd-pro-field__badge">
							{ __( 'Pro', 'order-delivery-date' ) }
						</span>
						<a
							href={ upgradeUrl }
							target="_blank"
							rel="noopener noreferrer"
							className="orddd-pro-field__link"
							onClick={ ( e ) => e.stopPropagation() }
						>
							{ __( 'Upgrade', 'order-delivery-date' ) }
						</a>
					</>
				) }
			</label>
		) ) }
	</div>
);

export default InlineRadios;
