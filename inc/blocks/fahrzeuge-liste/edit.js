( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;

	wp.blocks.registerBlockType( 'elfzwo/fahrzeuge-liste', {
		edit: function () {
			return el( ServerSideRender, { block: 'elfzwo/fahrzeuge-liste' } );
		},
		save: function () {
			return null;
		},
	} );
} )();
