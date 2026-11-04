import { __ } from '@wordpress/i18n';
import { ProSectionNotice } from '../../../components/ProNotice';

// ── Panel4AdditionalCharges ───────────────────────────────────────────────────

const Panel4AdditionalCharges = () => (
	<ProSectionNotice
		feature={ __( 'Set same day, next day, and rush hour delivery charges.', 'order-delivery-date' ) }
	/>
);

export default Panel4AdditionalCharges;
