/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n'; 
import { useBlockProps } from '@wordpress/block-editor';
import { ComboboxControl, PanelBody } from '@wordpress/components';
import { ValidatedTextInput } from '@woocommerce/blocks-checkout';

/**
 * Internal dependencies
 */

export const Edit = ({ attributes, setAttributes }) => {
	const blockProps = useBlockProps();
	return (
		<div {...blockProps} >
			<div 
				className={ 'orddd-datepicker-fields' } 
			>
				<ValidatedTextInput
					id="e_deliverydate"
					type="text"
					required={false}
					className={'orddd-datepicker'}
					label={
						'Delivery Date'
					}
					value={ '' }
				/>

				<div
					id={ 'orddd_lite_time_slot' }
					className={ 'wc-block-components-combobox' }
				>
					<ComboboxControl
						className={ 'wc-block-components-combobox-control' }
						label={ 'Time Slot' }
						onFilterValueChange={ () => null }
						options={ [] }
						value={ '' }
						allowReset={ false }
					/>
				</div>
			</div>
		</div>
	);
};
