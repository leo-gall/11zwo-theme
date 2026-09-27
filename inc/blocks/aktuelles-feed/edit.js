( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;

	wp.blocks.registerBlockType( 'elfzwo/aktuelles-feed', {
		edit: function () {
			return el( ServerSideRender, { block: 'elfzwo/aktuelles-feed' } );
		},
		save: function () {
			return null;
		},
	} );
} )();
