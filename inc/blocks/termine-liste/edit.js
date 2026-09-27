( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;

	wp.blocks.registerBlockType( 'elfzwo/termine-liste', {
		edit: function () {
			return el( ServerSideRender, { block: 'elfzwo/termine-liste' } );
		},
		save: function () {
			return null;
		},
	} );
} )();
