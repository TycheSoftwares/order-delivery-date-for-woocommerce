/**
 * WordPress dependencies
 */
import { CardBody, Tooltip, Icon } from '@wordpress/components';
import { info }          from '@wordpress/icons';

/**
 * A single settings row: bold label on the left,
 * optional info tooltip + field control on the right.
 *
 * @param {string}      label    - Row label text.
 * @param {string}      [tooltip] - Text shown in the ⓘ tooltip. Omit to hide icon.
 * @param {ReactNode}   children - The field control(s).
 */
const SettingRow = ( { label, tooltip, children } ) => (
	<CardBody size="none" className="osp-row">
		<div className="osp-row-label">
			<span className="osp-row-label-text">{ label }</span>
		</div>

		<div className="osp-row-field">
			{ tooltip && (
				<Tooltip text={ tooltip }>
					<span className="osp-info-icon" tabIndex={ 0 } aria-label="More information">
						<Icon icon={ info } size={ 18 } />
					</span>
				</Tooltip>
			) }
			<div className="osp-row-field-content">
				{ children }
			</div>
		</div>
	</CardBody>
);

export default SettingRow;
