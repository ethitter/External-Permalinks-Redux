/* global externalPermalinksReduxConfig */

import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { link } from '@wordpress/icons';
import { registerPlugin } from '@wordpress/plugins';

import PanelBody from './panel-body';

const { postTypes } = externalPermalinksReduxConfig;
const slug = 'external-permalinks-redux';

/**
 * Render panel view.
 *
 * @param {Object} props          Component props.
 * @param {string} props.postType Post type.
 * @return {JSX.Element|null} Sidebar panel.
 */
const View = ( { postType } ) => {
	if ( ! postType ) {
		return null;
	}

	return (
		<PluginDocumentSettingPanel
			name={ slug }
			title={ postTypes[ postType ] }
			className={ slug }
		>
			<PanelBody />
		</PluginDocumentSettingPanel>
	);
};

/**
 * HOC to provide the post type.
 */
const Panel = compose( [
	withSelect( ( select ) => {
		const { type: postType } = select( 'core/editor' ).getCurrentPost();

		return {
			postType,
		};
	} ),
] )( View );

registerPlugin( slug, {
	icon: link,
	render: Panel,
} );
