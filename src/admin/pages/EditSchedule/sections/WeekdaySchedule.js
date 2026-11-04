import { __ } from '@wordpress/i18n';
import { ProSectionNotice } from '../../../components/ProNotice';

// ── Panel5WeekdaySchedule ─────────────────────────────────────────────────────

const Panel5WeekdaySchedule = () => (
	<ProSectionNotice
		feature={ __( 'Configure per-weekday same/next day cutoffs, charges, and minimum delivery times.', 'order-delivery-date' ) }
	/>
);

export default Panel5WeekdaySchedule;
