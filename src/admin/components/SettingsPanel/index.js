/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState, useRef, useCallback } from '@wordpress/element';
import { Card, CardHeader, CardDivider, Button, Icon, Notice } from '@wordpress/components';
import { chevronDown, chevronUp } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import SettingRow from './SettingRow';

export { SettingRow };

/**
 * A standalone accordion card — a Card with a clickable header that toggles its content.
 *
 * @param {string}    title    - Header title.
 * @param {ReactNode} children - Card content.
 */
export const AccordionCard = ( { title, children, defaultOpen = true, badge } ) => {
	const [ isOpen, setIsOpen ] = useState( defaultOpen );

	return (
		<Card size="none" className="osp-card">
			<CardHeader
				className="osp-card-header"
				onClick={ () => setIsOpen( ( o ) => ! o ) }
			>
				<span className="osp-card-header-title">
					{ title }
					{ badge && <span className="osp-card-header-badge">{ badge }</span> }
				</span>
				<Icon icon={ isOpen ? chevronUp : chevronDown } />
			</CardHeader>
			<CardDivider />
			{ isOpen && children }
		</Card>
	);
};

/**
 * Generic settings panel with a title, subtitle, card of rows, and save buttons.
 *
 * @param {string}    title        - Page/section title.
 * @param {ReactNode} [subtitle]   - Subtitle text or JSX (supports links).
 * @param {Function}  onSave       - Called when either Save Settings button is clicked.
 * @param {boolean}   [isSaving]   - Shows spinner on the button while true.
 * @param {string}    [cardHeader] - Optional card header title. When provided the card becomes an accordion.
 * @param {boolean}   [noCard]     - When true, children are rendered directly without a card wrapper.
 * @param {ReactNode} children     - <SettingRow> components or <AccordionCard> sections.
 */
const SettingsPanel = ( { title, subtitle, onSave, isSaving = false, cardHeader, noCard = false, onBack, backLabel, headerAction, children } ) => {
	const sentinelRef             = useRef( null );
	const savedTimerRef           = useRef( null );
	const [ isSticky,  setIsSticky  ] = useState( false );
	const [ showSaved, setShowSaved ] = useState( false );

	// Intersection observer for sticky footer.
	// rootMargin shrinks the detection zone so the sticky appears slightly before
	// the footer fully exits view — this covers short pages like Common Schedule Settings.
	const setSentinel = useCallback( ( el ) => {
		sentinelRef.current = el;
		if ( ! el ) return;
		const observer = new IntersectionObserver(
			( [ entry ] ) => setIsSticky( ! entry.isIntersecting ),
			{ threshold: 0, rootMargin: '0px 0px -80px 0px' }
		);
		observer.observe( el );
	}, [] );

	// Wrap onSave: show "Settings saved." when the returned Promise resolves.
	const handleSave = useCallback( () => {
		const result = onSave?.();
		const show = () => {
			setShowSaved( true );
			clearTimeout( savedTimerRef.current );
			savedTimerRef.current = setTimeout( () => setShowSaved( false ), 3000 );
		};
		if ( result && typeof result.then === 'function' ) {
			result.then( show );
		} else {
			show();
		}
	}, [ onSave ] );

	const SaveButton = () => (
		<Button
			className="osp-save-btn orddd-save"
			onClick={ handleSave }
			isBusy={ isSaving }
			disabled={ isSaving }
		>
			{ __( 'Save Settings', 'order-delivery-date' ) }
		</Button>
	);

	const cardContent = noCard ? children : (
		cardHeader
			? <AccordionCard title={ cardHeader }>{ children }</AccordionCard>
			: <Card size="none" className="osp-card">{ children }</Card>
	);

	return (
		<div className="osp-wrap">

			{ /* ── Page header ── */ }
			<div className="osp-page-header">
				<div className="osp-page-header-text">
					<h1 className="osp-title">{ title }</h1>
					{ subtitle && (
						<p className="osp-subtitle">{ subtitle }</p>
					) }
				</div>
				{ headerAction && headerAction }
			</div>

			{ /* ── Settings card(s) ── */ }
			{ cardContent }

			{ /* ── Inline bottom button (always in DOM, used as scroll sentinel) ── */ }
			<div className="osp-page-footer" ref={ setSentinel }>
				{ showSaved && ! isSticky && (
					<Notice
						status="success"
						isDismissible
						onRemove={ () => setShowSaved( false ) }
						className="osp-inline-notice"
					>
						{ __( 'Settings saved.', 'order-delivery-date' ) }
					</Notice>
				) }
				{ onSave && <SaveButton /> }
			</div>

			{ /* ── Sticky save button — fixed bottom when inline button is out of view ── */ }
			{ isSticky && ( onSave || onBack ) && (
				<div className="osp-sticky-footer">
					{ onBack && (
						<Button
							variant="link"
							className="osp-sticky-back-btn"
							onClick={ onBack }
							style={ { fontSize: '15px' } }
						>
							{ backLabel || __( '← Back', 'order-delivery-date' ) }
						</Button>
					) }
					<div style={ { display: 'flex', alignItems: 'center', gap: '12px', marginLeft: 'auto' } }>
						
						{ showSaved && (
							<Notice
								status="success"
								isDismissible
								onRemove={ () => setShowSaved( false ) }
								className="osp-sticky-notice"
							>
								{ __( 'Settings saved.', 'order-delivery-date' ) }
							</Notice>
						) }
						{ onSave && <SaveButton /> }
					</div>
				</div>
			) }

		</div>
	);
};

export default SettingsPanel;
