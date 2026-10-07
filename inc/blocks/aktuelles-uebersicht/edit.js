( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;

	wp.blocks.registerBlockType( 'elfzwo/aktuelles-uebersicht', {
		edit: function () {
			return el( ServerSideRender, { block: 'elfzwo/aktuelles-uebersicht' } );
		},
		save: function () {
			return null;
		},
	} );
} )();
