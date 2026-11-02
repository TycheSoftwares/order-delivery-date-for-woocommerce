/** Native multi-select. */
const MultiSelect = ( { options, value, onChange, placeholder } ) => {
	const handleChange = ( e ) => {
		onChange( Array.from( e.target.selectedOptions ).map( ( o ) => o.value ) );
	};
	return (
		<select
			multiple
			value={ value }
			onChange={ handleChange }
			size={ Math.min( 6, Math.max( 3, options.length ) ) }
			style={ { width: '100%', border: '1px solid #a7acb1', borderRadius: '4px', padding: '4px', fontSize: '13px' } }
		>
			{ placeholder && <option value="" disabled>{ placeholder }</option> }
			{ options.map( ( o ) => (
				<option key={ o.value } value={ o.value }>{ o.label }</option>
			) ) }
		</select>
	);
};

export default MultiSelect;
