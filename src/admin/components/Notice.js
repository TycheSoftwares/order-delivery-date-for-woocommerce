/**
 * Notice – dismissible admin notice matching Pro design.
 */

import { __ } from '@wordpress/i18n';

export default function Notice( { type = 'success', message, onDismiss } ) {
	return (
		<div className={ `orddd-notice orddd-notice--${ type }` } role="alert">
			<span className="orddd-notice__text">{ message }</span>
			{ onDismiss && (
				<button
					type="button"
					className="orddd-notice__dismiss"
					onClick={ onDismiss }
					aria-label={ __( 'Dismiss', 'order-delivery-date' ) }
				>
					&#x2715;
				</button>
			) }
		</div>
	);
}
