/**
 * SaveButton – submit button with spinner while saving, Pro-style.
 */

import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function SaveButton( {
	isSaving,
	onClick,
	label = __( 'Save Settings', 'order-delivery-date' ),
} ) {
	return (
		<button
			type={ onClick ? 'button' : 'submit' }
			className="orddd-save-btn"
			onClick={ onClick }
			disabled={ isSaving }
		>
			{ isSaving && <Spinner /> }
			{ isSaving ? __( 'Saving…', 'order-delivery-date' ) : label }
		</button>
	);
}
