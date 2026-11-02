/**
 * useSettings – shared hook for fetching and saving settings via the REST API.
 */

import { useState, useEffect } from '@wordpress/element';
import { apiGet, apiPost } from '../api';

/**
 * @typedef {Object} UseSettingsReturn
 * @property {Object|null} settings   All settings from GET /orddd/v1/settings.
 * @property {boolean}     isSaving   True while a save request is in flight.
 * @property {Object|null} notice     { type: 'success'|'error', message: string }
 * @property {Function}    save       save(key, payload) → triggers POST.
 * @property {Function}    clearNotice
 * @property {Function}    reload     Re-fetches settings from the API.
 */

/**
 * Hook: fetch all settings on mount, expose save helper.
 *
 * @return {UseSettingsReturn}
 */
export function useSettings() {
	const [ settings, setSettings ] = useState( null );
	const [ isSaving, setIsSaving ] = useState( false );
	const [ notice, setNotice ] = useState( null );

	const fetchSettings = () => {
		apiGet( '/settings' )
			.then( ( data ) => setSettings( data ) )
			.catch( ( err ) =>
				setNotice( {
					type: 'error',
					message: err?.message ?? 'Failed to load settings.',
				} )
			);
	};

	useEffect( () => {
		fetchSettings();
	}, [] );

	/**
	 * Save a settings group.
	 *
	 * @param {string} key     Settings group key (e.g. 'default_schedule').
	 * @param {Object} payload Fields to save.
	 * @return {Promise}
	 */
	const save = ( key, payload ) => {
		setIsSaving( true );
		setNotice( null );

		return apiPost( '/settings', { key, ...payload } )
			.then( ( response ) => {
				setNotice( {
					type: 'success',
					message: response?.message ?? 'Settings saved.',
				} );
				// Merge the updated group back into local state.
				if ( response?.data ) {
					setSettings( ( prev ) => ( { ...prev, ...response.data } ) );
				}
			} )
			.catch( ( err ) => {
				setNotice( {
					type: 'error',
					message: err?.message ?? 'Failed to save settings.',
				} );
			} )
			.finally( () => setIsSaving( false ) );
	};

	const clearNotice = () => setNotice( null );

	return { settings, isSaving, notice, save, clearNotice, reload: fetchSettings };
}
