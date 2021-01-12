/** Register custom styles for the button, remove the outline style */

wp.domReady( function() {
	wp.blocks.registerBlockStyle( 'core/button', {
		name: 'arrow-cta',
		label: 'Arrow Link',
	} );

	wp.blocks.registerBlockStyle( 'core/list', {
		name: 'no-bullets',
		label: 'No Bullets',
	} );

	wp.blocks.registerBlockStyle( 'core/separator', {
		name: 'no-margin',
		label: 'No Margin',
	} );

	wp.blocks.unregisterBlockStyle( 'core/button', 'outline' );
} );

//Set the default dimratio on the cover block to 0.
function setBlockDefaults( settings, name ) {
	if ( name === 'core/cover' ) {
		if ( settings.attributes && settings.attributes.dimRatio ) {
			settings.attributes.dimRatio.default = 0;
		}
	} else if ( name === 'core/separator' ) {
		if ( settings.supports ) {
			settings.supports.align = [ 'wide', 'full' ];
		} else {
			settings.supports = {
				align: [ 'wide', 'full' ],
			};
		}
	}

	return settings;
}

wp.hooks.addFilter(
	'blocks.registerBlockType',
	'wprig-theme',
	setBlockDefaults
);
