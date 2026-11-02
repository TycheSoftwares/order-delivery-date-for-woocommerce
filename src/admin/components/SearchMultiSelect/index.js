/**
 * SearchMultiSelect
 *
 * Multi-value token picker built on WP's TokenField.
 * Internally operates on display labels; converts to/from stored values (IDs/slugs)
 * via a ref-based bidirectional map that is updated on every search.
 *
 * Props:
 *   value            {string[]}  Selected stored values (IDs / slugs).
 *   onChange         {Function}  (string[]) => void
 *   fetchSuggestions {Function}  async (query: string) => { value, label }[]
 *   resolveLabel     {Function}  (value: string) => string  — label from pre-loaded data
 *   placeholder      {string}
 */

import { useState, useRef } from '@wordpress/element';
import { FormTokenField, Spinner } from '@wordpress/components';

const SearchMultiSelect = ( {
	value          = [],
	onChange,
	fetchSuggestions,
	resolveLabel,
	placeholder    = 'Search…',
} ) => {
	// Bidirectional maps kept in refs so updates don't cause re-renders.
	const valueToLabel = useRef( {} ); // { storedValue → displayLabel }
	const labelToValue = useRef( {} ); // { displayLabel → storedValue }

	const [ suggestions, setSuggestions ] = useState( [] );
	const [ searching,   setSearching   ] = useState( false );
	const timerRef = useRef( null );

	// Ensure pre-loaded items are always in the maps.
	value.forEach( ( v ) => {
		if ( valueToLabel.current[ v ] ) return; // already cached
		const label = resolveLabel( v );
		if ( label && label !== v ) {
			valueToLabel.current[ v ]     = label;
			labelToValue.current[ label ] = v;
		}
	} );

	// TokenField works with plain strings — convert stored values → display labels.
	const tokenLabels = value.map( ( v ) => valueToLabel.current[ v ] || resolveLabel( v ) || v );

	// ── Debounced search ──────────────────────────────────────────────────────

	const handleInputChange = ( query ) => {
		clearTimeout( timerRef.current );

		if ( ! query.trim() ) {
			setSuggestions( [] );
			return;
		}

		timerRef.current = setTimeout( async () => {
			setSearching( true );
			try {
				const results = await fetchSuggestions( query );

				// Populate / update the bidirectional maps from search results.
				results.forEach( ( r ) => {
					valueToLabel.current[ r.value ] = r.label;
					labelToValue.current[ r.label ] = r.value;
				} );

				// Exclude already-selected items from the dropdown.
				setSuggestions(
					results
						.filter( ( r ) => ! value.includes( r.value ) )
						.map( ( r ) => r.label )
				);
			} catch {
				setSuggestions( [] );
			} finally {
				setSearching( false );
			}
		}, 350 );
	};

	// ── Token change ─────────────────────────────────────────────────────────

	const handleChange = ( newLabels ) => {
		// Convert display labels back to stored values; drop any unrecognised tokens.
		const newValues = newLabels
			.map( ( label ) => labelToValue.current[ label ] ?? null )
			.filter( ( v ) => v !== null );

		onChange( newValues );
	};

	// ── Render ────────────────────────────────────────────────────────────────

	return (
		<div className="sms-wrap">
			<FormTokenField
				value={ tokenLabels }
				suggestions={ suggestions }
				onInputChange={ handleInputChange }
				onChange={ handleChange }
				placeholder={ value.length ? 'Add more…' : placeholder }
				__nextHasNoMarginBottom
			/>
			{ searching && <Spinner style={ { marginTop: '6px' } } /> }
		</div>
	);
};

export default SearchMultiSelect;
