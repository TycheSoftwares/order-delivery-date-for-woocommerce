import { __ } from '@wordpress/i18n';
import { ProSectionNotice } from '../../../components/ProNotice';

// ── PanelTimeSlotCutoffs ──────────────────────────────────────────────────────

const PanelTimeSlotCutoffs = () => (
	<ProSectionNotice
		feature={ __( 'Set same day and next day cutoff times per individual time slot.', 'order-delivery-date' ) }
	/>
);

export default PanelTimeSlotCutoffs;
