/**
 * A thin wrapper around WordPress's built-in TinyMCE editor (wp.editor).
 *
 * Props:
 *   id       {string}   - Unique HTML id for the underlying <textarea>. Must be
 *                         lowercase, no spaces (TinyMCE requirement).
 *   value    {string}   - Controlled value.
 *   onChange {Function} - Called with the new HTML string on every editor change.
 *   rows     {number}   - Visible rows for the fallback <textarea> (default 10).
 */

import { useEffect, useRef } from '@wordpress/element';

const WpEditor = ( { id = 'orddd-wp-editor', value = '', onChange, rows = 10 } ) => {
	const initialValue = useRef( value );

	useEffect( () => {
		if ( ! window.wp?.editor ) return;

		// wp.editor.initialize renders both TinyMCE and QuickTags.
		window.wp.editor.initialize( id, {
			tinymce: {
				wpautop:  true,
				// Sync editor content to React state on every keyup / change.
				setup( editor ) {
					editor.on( 'change keyup', () => {
						onChange?.( editor.getContent() );
					} );
				},
			},
			quicktags: true,
			mediaButtons: true,
		} );

		return () => {
			// Clean up the editor instance when the component unmounts.
			window.wp.editor.remove( id );
		};
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [ id ] );

	// When the controlled value changes externally, push it into the editor.
	useEffect( () => {
		if ( ! window.wp?.editor || ! window.tinymce ) return;
		const editor = window.tinymce.get( id );
		if ( editor && editor.getContent() !== value ) {
			editor.setContent( value );
		}
	}, [ id, value ] );

	// The <textarea> is the mount point for TinyMCE.
	// defaultValue is used so TinyMCE picks up the initial content on init.
	return (
		<textarea
			id={ id }
			defaultValue={ initialValue.current }
			rows={ rows }
			style={ { width: '100%' } }
		/>
	);
};

export default WpEditor;
