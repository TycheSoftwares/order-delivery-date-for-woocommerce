import { TextControl } from '@wordpress/components';

/** Time input using WP TextControl. */
const TimeInput = ( { value, onChange, ...rest } ) => (
	<TextControl
		type="time"
		value={ value }
		onChange={ onChange }
		__nextHasNoMarginBottom
		{ ...rest }
	/>
);

export default TimeInput;
