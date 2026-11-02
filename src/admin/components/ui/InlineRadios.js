/** Inline radio-button group (WP RadioControl stacks; this keeps them horizontal). */
const InlineRadios = ( { name, options, value, onChange } ) => (
	<div style={ { display: 'flex', gap: '20px', flexWrap: 'wrap' } }>
		{ options.map( ( opt ) => (
			<label
				key={ opt.value }
				style={ { display: 'flex', alignItems: 'center', gap: '6px', cursor: 'pointer', fontSize: '13px' } }
			>
				<input
					type="radio"
					name={ name }
					value={ opt.value }
					checked={ value === opt.value }
					onChange={ () => onChange( opt.value ) }
				/>
				{ opt.label }
			</label>
		) ) }
	</div>
);

export default InlineRadios;
