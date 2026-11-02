/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Button } from '@wordpress/components';

/**
 * External dependencies
 */
import { NavLink } from 'react-router-dom';

/**
 * Internal dependencies
 */


const allMenuItems = [
	{ key: 'home',              label: __( 'Home', 'order-delivery-date' ),                       path: '/home'              },
	{ key: 'delivery-schedule', label: __( 'Delivery Schedules', 'order-delivery-date' ), path: '/delivery-schedule' },
	{ key: 'holidays',          label: __( 'Holidays', 'order-delivery-date' ),                   path: '/holidays'           },
	{ key: 'settings',          label: __( 'Settings', 'order-delivery-date' ),                   path: '/settings'           },
	{ key: 'appearance',        label: __( 'Appearance', 'order-delivery-date' ),                 path: '/appearance'         },
	{ key: 'integrations',      label: __( 'Integrations', 'order-delivery-date' ),               path: '/integrations'       },
	{ key: 'reminders',         label: __( 'Reminders', 'order-delivery-date' ),                  path: '/reminders'          },
	{ key: 'view-orders',       label: __( 'Order Calendar', 'order-delivery-date' ),             path: '/view-orders'        },
];


const pluginUrl = window.ordddAdminData?.plugin_url ?? '';

const Header = () => {
	const [ menuOpen, setMenuOpen ] = useState( false );

	return (
		<div className="ordd-header">
			<div className="header-wrap">

				<div className="branding">
					<a href={ pluginUrl }>
						<img
							src={ `${ pluginUrl }images/ordd-logo.svg` }
							alt={ __( 'Order Delivery Date', 'order-delivery-date' ) }
						/>
					</a>
				</div>

				<nav className="ordd-navigation">
					<Button
						className="ordd-nav-toggler"
						onClick={ () => setMenuOpen( ( open ) => ! open ) }
						aria-expanded={ menuOpen }
						aria-label={ __( 'Toggle navigation', 'order-delivery-date' ) }
					>
						<span className="ordd-nav-toggler-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
								<path fill="none" d="M0 0h24v24H0z" />
								<path d="M3 4h18v2H3V4zm0 7h12v2H3v-2zm0 7h18v2H3v-2z" fill="rgba(255,255,255,1)" />
							</svg>
						</span>
					</Button>

					<div className={ `ordd-nav-collapse${ menuOpen ? ' ordd-nav-collapse--open' : '' }` }>
						<ul className="ordd-nav-menu">
							{ allMenuItems.map( ( item ) => (
								<li key={ item.key }>
									<NavLink
										to={ item.path }
										className={ ( { isActive } ) =>
											'ordd-nav-link' + ( isActive ? ' active' : '' )
										}
										end={ item.path === '/' }
									>
										{ item.label }
									</NavLink>
								</li>
							) ) }
						</ul>
					</div>
				</nav>


			</div>
		</div>
	);
};

export default Header;
