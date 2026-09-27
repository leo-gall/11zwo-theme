( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;

	wp.blocks.registerBlockType( 'elfzwo/aktuelle-einsaetze', {
		edit: function () {
			return el( ServerSideRender, { block: 'elfzwo/aktuelle-einsaetze' } );
		},
		save: function () {
			return null;
		},
	} );
} )();
