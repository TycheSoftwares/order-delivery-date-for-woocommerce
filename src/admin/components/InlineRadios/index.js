/**
 * Renders a group of radio buttons in a single horizontal row.
 *
 * Drop-in replacement for @wordpress/components RadioControl when you need
 * options displayed side-by-side instead of stacked vertically.
 *
 * @param {string}   selected  - The currently selected value.
 * @param {Array}    options   - Array of { value, label } objects.
 * @param {Function} onChange  - Called with the new value string when selection changes.
 * @param {string}   [name]    - HTML name attribute shared by all inputs. Auto-generated if omitted.
 */
import { useRef } from '@wordpress/element';

const instanceCounter = { n: 0 };

const InlineRadios = ( { selected, options, onChange, name } ) => {
	const nameRef = useRef( name || `inline-radios-${ ++instanceCounter.n }` );
	const groupName = nameRef.current;

	return (
		<div className="orddd-inline-radios">
			{ options.map( ( option ) => {
				const id = `${ groupName }-${ option.value }`;
				return (
					<label key={ option.value } className="orddd-inline-radios__option" htmlFor={ id }>
						<input
							type="radio"
							id={ id }
							name={ groupName }
							value={ option.value }
							checked={ selected === option.value }
							onChange={ () => onChange( option.value ) }
							className="orddd-inline-radios__input"
						/>
						{ option.label }
					</label>
				);
			} ) }
		</div>
	);
};

export default InlineRadios;
