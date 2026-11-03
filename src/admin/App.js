/**
 * App – matches Pro's routing structure.
 *
 * Uses HashRouter (same as Pro) so URLs look like:
 *   admin.php?page=order_delivery_date_lite#/home
 *   admin.php?page=order_delivery_date_lite#/appearance/field-appearance
 */

import { HashRouter, Routes, Route, Navigate } from 'react-router-dom';
import { Component } from '@wordpress/element';

class RouteErrorBoundary extends Component {
	constructor( props ) {
		super( props );
		this.state = { error: null };
	}
	static getDerivedStateFromError( error ) {
		return { error };
	}
	render() {
		if ( this.state.error ) {
			return (
				<div style={ { padding: '32px', color: '#cc1818', background: '#fff8f8', border: '1px solid #f5c2c7', margin: '24px', borderRadius: '4px' } }>
					<strong>React error on this page:</strong>
					<pre style={ { marginTop: '8px', whiteSpace: 'pre-wrap', fontSize: '12px' } }>
						{ this.state.error.message }{ '\n' }{ this.state.error.stack }
					</pre>
				</div>
			);
		}
		return this.props.children;
	}
}
import Header from './components/Header';
import HomePage from './pages/HomePage';
import DeliverySchedules from './pages/DeliverySchedules';
import EditSchedule from './pages/EditSchedule';
import Holidays from './pages/Holidays';
import Appearance from './pages/Appearance';
import Settings from './pages/Settings';
import OrderCalendar from './pages/OrderCalendar';
import IntegrationsPage from './pages/IntegrationsPage';
import RemindersPage from './pages/RemindersPage';

/** Inner layout — needs to be inside <HashRouter> to use useLocation */
function AppContent() {
	return (
		<div className="orddd-admin-app">
			<Header />
			<RouteErrorBoundary>
				<Routes>
					<Route path="/" element={ <Navigate to="/delivery-schedule" replace /> } />
					<Route path="/home/:tab?"              element={ <HomePage /> } />
					<Route path="/settings/:tab?"      element={ <Settings /> } />
					<Route path="/appearance/:tab?"    element={ <Appearance /> } />
					<Route path="/integrations/:tab?"  element={ <IntegrationsPage /> } />
					<Route path="/reminders/:tab?"     element={ <RemindersPage /> } />
					<Route path="/holidays"            element={ <Holidays /> } />
					<Route path="/delivery-schedule"        element={ <DeliverySchedules /> } />
					<Route path="/edit-schedule/:id"        element={ <EditSchedule /> } />
					<Route path="/view-orders"         element={ <OrderCalendar /> } />
				</Routes>
			</RouteErrorBoundary>
		</div>
	);
}

export default function App() {
	return (
		<HashRouter>
			<AppContent />
		</HashRouter>
	);
}
