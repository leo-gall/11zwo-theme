( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;

	wp.blocks.registerBlockType( 'elfzwo/downloads-liste', {
		edit: function () {
			return el( ServerSideRender, { block: 'elfzwo/downloads-liste' } );
		},
		save: function () {
			return null;
		},
	} );
} )();
