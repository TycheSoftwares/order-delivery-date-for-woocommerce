/**
 * apiFetch wrapper helpers for the Lite admin SPA.
 */

import apiFetch from '@wordpress/api-fetch';

const BASE = '/orddd/v1';

/**
 * GET request helper.
 *
 * @param {string} path Relative path under /orddd/v1.
 * @return {Promise}
 */
export function apiGet( path ) {
	return apiFetch( { path: `${ BASE }${ path }` } );
}

/**
 * POST request helper.
 *
 * @param {string} path Relative path under /orddd/v1.
 * @param {Object} data Request body.
 * @return {Promise}
 */
export function apiPost( path, data ) {
	return apiFetch( {
		path: `${ BASE }${ path }`,
		method: 'POST',
		data,
	} );
}

/**
 * DELETE request helper.
 *
 * @param {string} path   Relative path under /orddd/v1.
 * @param {Object} params Query params appended to the URL.
 * @return {Promise}
 */
export function apiDelete( path, params = {} ) {
	const query = new URLSearchParams( params ).toString();
	return apiFetch( {
		path: `${ BASE }${ path }${ query ? '?' + query : '' }`,
		method: 'DELETE',
	} );
}
