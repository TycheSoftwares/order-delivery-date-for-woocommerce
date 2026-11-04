import Spinner from './spinner';
import classnames from 'classnames';

const LoadingMask = ( {
	children,
	className,
	screenReaderLabel,
	showSpinner = false,
	isLoading = true,
}) => {
	return (
		<div
			className={ classnames( className, {
				'wc-block-components-loading-mask': isLoading,
			} ) }
		>
			{ isLoading && showSpinner && <Spinner /> }
			<div
				className={ classnames( {
					'wc-block-components-loading-mask__children': isLoading,
				} ) }
				aria-hidden={ isLoading }
			>
				{ children }
			</div>
			{ isLoading && (
				<span className="screen-reader-text">
					{ screenReaderLabel ||
						__( 'Loading…', 'woo-gutenberg-products-block' ) }
				</span>
			) }
		</div>
	);
};

export default LoadingMask;