// ── Helpers ───────────────────────────────────────────────────────────────────

export const toTimeInput = ( hours, minutes ) => {
	if ( ! hours || hours === '0' || hours === 0 ) return '';
	const h = String( parseInt( hours, 10 ) ).padStart( 2, '0' );
	const m = String( parseInt( minutes || 0, 10 ) ).padStart( 2, '0' );
	return `${ h }:${ m }`;
};

export const fromTimeInput = ( value ) => {
	if ( ! value ) return { after_hours: '', after_minutes: '00' };
	const [ h, m ] = value.split( ':' );
	return { after_hours: String( parseInt( h, 10 ) ), after_minutes: m || '00' };
};

export const getAdminData = () => window.ordddAdminData || {};
