/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { FormTokenField, __experimentalVStack as VStack } from '@wordpress/components';
import { useRef, useState, useEffect } from '@wordpress/element';

/**
 * Maps type to its ORDD_Store REST API path.
 *
 * Endpoints (orddd/v1 namespace):
 *   GET /orddd/v1/store/products    ?search=&limit=  or  ?ids[]=
 *   GET /orddd/v1/store/categories  ?search=&limit=  or  ?slugs[]=
 *   GET /orddd/v1/store/tags        ?search=&limit=  or  ?slugs[]=
 *
 * @param {string} type 'products' | 'categories' | 'tags' | 'variations'
 * @returns {string} REST path without query string.
 */
const getApiPath = ( type ) => {
    switch ( type ) {
        case 'categories':
            return '/orddd/v1/store/categories';
        case 'tags':
            return '/orddd/v1/store/tags';
        case 'variations':
        case 'products':
        default:
            return '/orddd/v1/store/products';
    }
};

/**
 * Whether the type stores numeric IDs (true) or slugs (false).
 * Products are stored as IDs so PHP can match them with cart product IDs.
 */
const usesIds = ( type ) => type === 'products' || type === 'variations';

/**
 * TokenField — async multi-select built on WP's FormTokenField.
 *
 * Fetches product / category / tag data from the ORDD_Store REST API as the
 * user types. Stores and returns a string[] of slugs via `field.onChange`.
 *
 * Props:
 * @param {string}   label  Label shown above the field.
 * @param {string}   type   'products' | 'categories' | 'tags' | 'variations'
 * @param {Object}   field  react-hook-form field ({ value: string[], onChange }).
 * @param {*}        error  Truthy value adds 'show_error' class.
 *
 * @returns {JSX.Element}
 */
function TokenField( { label, type, field, error } ) {
    const { onChange, value = [] } = field;

    // Bidirectional maps — updated on every search, never cause re-renders.
    const slugToLabel = useRef( {} ); // { slug → displayLabel }
    const labelToSlug = useRef( {} ); // { displayLabel → slug }

    const [ suggestions, setSuggestions ] = useState( [] );
    // Incremented after the initial slug→label fetch to trigger a re-render.
    const [ , setResolved ] = useState( 0 );
    const timerRef = useRef( null );

    // ── Resolve labels for pre-existing values ───────────────────────────────
    // Re-runs whenever the value list changes so labels are always fetched,
    // even if form data arrives after the component has already mounted.
    // Products use `ids[]`; categories/tags use `slugs[]`.

    const valueKey = value.join( ',' );

    useEffect( () => {
        if ( ! value.length ) return;

        const missing = value.filter( ( v ) => ! slugToLabel.current[ v ] );
        if ( ! missing.length ) return;

        const paramName = usesIds( type ) ? 'ids' : 'slugs';
        const qs = missing.map( ( v ) => `${ paramName }[]=${ encodeURIComponent( v ) }` ).join( '&' );

        apiFetch( { path: `${ getApiPath( type ) }?${ qs }` } )
            .then( ( data ) => {
                data.forEach( ( item ) => {
                    const key = usesIds( type ) ? String( item.id ) : item.slug;
                    slugToLabel.current[ key ]       = String( item.title );
                    labelToSlug.current[ item.title ] = key;
                } );
                setResolved( ( n ) => n + 1 );
            } )
            .catch( () => {} );
    }, [ valueKey ] ); // eslint-disable-line react-hooks/exhaustive-deps

    // ── Debounced search ──────────────────────────────────────────────────────

    const handleInputChange = ( query ) => {
        clearTimeout( timerRef.current );

        if ( ! query.trim() ) {
            setSuggestions( [] );
            return;
        }

        timerRef.current = setTimeout( () => {
            const path = `${ getApiPath( type ) }?search=${ encodeURIComponent( query ) }`;

            apiFetch( { path } )
                .then( ( data ) => {
                    // Refresh the bidirectional maps from search results.
                    data.forEach( ( item ) => {
                        const key = usesIds( type ) ? String( item.id ) : item.slug;
                        slugToLabel.current[ key ]         = String( item.title );
                        labelToSlug.current[ item.title ]  = key;
                    } );

                    // Exclude already-selected values from the dropdown.
                    const selectedValues = new Set( value );
                    setSuggestions(
                        data
                            .filter( ( item ) => ! selectedValues.has( usesIds( type ) ? String( item.id ) : item.slug ) )
                            .map( ( item ) => String( item.title ) )
                    );
                } )
                .catch( () => setSuggestions( [] ) );
        }, 300 );
    };

    // ── Token change ──────────────────────────────────────────────────────────

    const handleChange = ( newLabels ) => {
        // Convert display labels back to slugs; drop any unresolved tokens.
        const newSlugs = newLabels
            .map( ( lbl ) => labelToSlug.current[ lbl ] ?? null )
            .filter( Boolean );

        onChange( newSlugs );
    };

    // ── Render ────────────────────────────────────────────────────────────────

    // FormTokenField works with plain strings — show label for each stored slug.
    // Falls back to the slug itself until the user searches and populates the map.
    const tokenLabels = value.map( ( slug ) => slugToLabel.current[ slug ] ?? slug );

    return (
        <VStack>
            { label && (
                <label
                    style={ {
                        fontSize: '11px',
                        fontWeight: '500',
                        lineHeight: '1.4',
                        textTransform: 'uppercase',
                    } }
                >
                    { label }
                </label>
            ) }
            <div className={ `orddd-token-field${ error ? ' show_error' : '' }` }>
                <FormTokenField
                    value={ tokenLabels }
                    suggestions={ suggestions }
                    onInputChange={ handleInputChange }
                    onChange={ handleChange }
                    placeholder={ __( 'Search…', 'order-delivery-date' ) }
                    __experimentalShowHowTo={ false }
                    __nextHasNoMarginBottom
                />
            </div>
        </VStack>
    );
}

export default TokenField;
