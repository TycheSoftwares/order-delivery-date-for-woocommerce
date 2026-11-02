/**
 * DeliverySchedulesPage – shows a table of schedules.
 * Lite has only the Default Schedule; custom schedules are a Pro feature.
 */

import { useNavigate } from 'react-router-dom';
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const upgradeUrl =
	window.ordddAdminData?.upgradeUrl ??
	'https://www.tychesoftwares.com/store/premium-plugins/order-delivery-date-for-woocommerce-pro-21/';

export default function DeliverySchedulesPage() {
	const navigate = useNavigate();

	return (
		<div className="orddd-page-wrap">
			<div className="osp-page-header">
				<div className="osp-page-header-text">
					<h1 className="osp-title">{ __( 'Delivery & Pickup Schedules', 'order-delivery-date' ) }</h1>
					<p className="osp-subtitle">
						{ __( 'Manage delivery schedules that control when customers can choose a delivery date.', 'order-delivery-date' ) }
					</p>
				</div>

				<div className="orddd-locked-toggle">
					<button
						type="button"
						className="components-button is-primary"
						disabled
						style={ { opacity: 0.5, cursor: 'not-allowed' } }
					>
						{ __( '+ Add Custom Schedule', 'order-delivery-date' ) }
					</button>
					<span className="orddd-locked-toggle__badge">
						{ __( 'Pro', 'order-delivery-date' ) }
					</span>
					<a
						href={ upgradeUrl }
						target="_blank"
						rel="noopener noreferrer"
						className="orddd-locked-toggle__link"
					>
						{ __( 'Upgrade to Pro', 'order-delivery-date' ) }
					</a>
				</div>
			</div>

			<table className="orddd-automation-table wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>{ __( 'Schedule Name', 'order-delivery-date' ) }</th>
						<th>{ __( 'Status', 'order-delivery-date' ) }</th>
						<th>{ __( 'Actions', 'order-delivery-date' ) }</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong>{ __( 'Default Schedule', 'order-delivery-date' ) }</strong></td>
						<td>
							<span className="orddd-schedule-status orddd-schedule-status--active">
								{ __( 'Active', 'order-delivery-date' ) }
							</span>
						</td>
						<td>
							<Button
								variant="secondary"
								onClick={ () => navigate( '/delivery-schedule/edit' ) }
							>
								{ __( 'Edit', 'order-delivery-date' ) }
							</Button>
						</td>
					</tr>
				</tbody>
			</table>

			<p className="orddd-schedules-upsell-hint">
				{ __( 'Need schedules per shipping method or product category? ', 'order-delivery-date' ) }
				<a href={ upgradeUrl } target="_blank" rel="noopener noreferrer">
					{ __( 'Upgrade to Pro', 'order-delivery-date' ) }
				</a>
				{ __( ' to create unlimited custom schedules.', 'order-delivery-date' ) }
			</p>
		</div>
	);
}
