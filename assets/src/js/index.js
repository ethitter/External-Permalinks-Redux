/* global externalPermalinksReduxConfig */

import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { link } from '@wordpress/icons';
import { registerPlugin } from '@wordpress/plugins';

const { postTypes } = externalPermalinksReduxConfig;
const slug = 'external-permalinks-redux';

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
			Hi
		</PluginDocumentSettingPanel>
	);
};

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
