/* global externalPermalinksReduxConfig */

import { TextControl, SelectControl } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { withSelect, withDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

const {
	metaKeys: { target: metaKeyTarget, type: metaKeyType },
	statusCodes,
} = externalPermalinksReduxConfig;

const View = ( { setTarget, setType, target, type } ) => {
	return (
		<>
			<TextControl
				label={ __(
					'Destination Address:',
					'external-permalinks-redux'
				) }
				help={ __(
					'To restore the original permalink, remove the link entered above.',
					'external-permalinks-redux'
				) }
				type="url"
				value={ target }
				onChange={ setTarget }
			/>

			<SelectControl
				label={ __( 'Redirect Type:', 'external-permalinks-redux' ) }
				options={ statusCodes }
				selected={ type }
				onChange={ setType }
			/>
		</>
	);
};

/**
 * HOC to provide meta values and methods for updating meta.
 */
const PanelBody = compose( [
	withSelect( ( select ) => {
		const { getEditedPostAttribute } = select( 'core/editor' );
		const meta = getEditedPostAttribute( 'meta' );

		return {
			target: meta[ metaKeyTarget ],
			type: meta[ metaKeyType ],
		};
	} ),
	withDispatch( ( dispatch ) => {
		const { editPost } = dispatch( 'core/editor' );

		const setTarget = ( target ) => {
			editPost( {
				meta: {
					[ metaKeyTarget ]: target.trim(),
				},
			} );
		};

		const setType = ( type ) => {
			editPost( {
				meta: {
					[ metaKeyType ]: parseInt( type, 10 ),
				},
			} );
		};

		return {
			setTarget,
			setType,
		};
	} ),
] )( View );

export default PanelBody;
