( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var RangeControl = wp.components.RangeControl;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType( 'elfzwo/join-timeline', {
		edit: function ( props ) {
			var a = props.attributes;
			var steps = a.steps || [];

			function set( key ) {
				return function ( v ) {
					var o = {};
					o[ key ] = v;
					props.setAttributes( o );
				};
			}
			function updateStep( idx, key, value ) {
				var next = steps.slice();
				var patch = {};
				patch[ key ] = value;
				next[ idx ] = Object.assign( {}, next[ idx ], patch );
				props.setAttributes( { steps: next } );
			}
			function removeStep( idx ) {
				var next = steps.slice();
				next.splice( idx, 1 );
				props.setAttributes( { steps: next } );
			}
			function moveStep( idx, dir ) {
				var target = idx + dir;
				if ( target < 0 || target >= steps.length ) { return; }
				var next = steps.slice();
				var tmp = next[ idx ];
				next[ idx ] = next[ target ];
				next[ target ] = tmp;
				props.setAttributes( { steps: next } );
			}
			function addStep() {
				props.setAttributes( { steps: steps.concat( [ { number: '', title: '', body: '', buttonText: '', buttonUrl: '' } ] ) } );
			}

			return el(
				'div', {},
				el(
					InspectorControls,
					{},
					el( PanelBody, { title: 'Timeline' },
						el( RangeControl, { label: 'Spalten', min: 2, max: 5, value: a.columns, onChange: set( 'columns' ) } )
					),
					el( PanelBody, { title: 'Schritte', initialOpen: true },
						steps.map( function ( step, idx ) {
							return el(
								'div',
								{ key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el( TextControl, { label: 'Nummer (leer + Button-Text = reiner CTA-Slot)', value: step.number, onChange: function ( v ) { updateStep( idx, 'number', v ); } } ),
								el( TextControl, { label: 'Titel', value: step.title, onChange: function ( v ) { updateStep( idx, 'title', v ); } } ),
								el( TextareaControl, { label: 'Text', value: step.body, onChange: function ( v ) { updateStep( idx, 'body', v ); } } ),
								el( TextControl, { label: 'Button-Text (optional)', value: step.buttonText, onChange: function ( v ) { updateStep( idx, 'buttonText', v ); } } ),
								el( TextControl, { label: 'Button-URL', value: step.buttonUrl, onChange: function ( v ) { updateStep( idx, 'buttonUrl', v ); } } ),
								el( Button, { variant: 'link', onClick: function () { moveStep( idx, -1 ); }, disabled: idx === 0 }, '↑ Hoch' ),
								el( Button, { variant: 'link', onClick: function () { moveStep( idx, 1 ); }, disabled: idx === steps.length - 1 }, '↓ Runter' ),
								el( Button, { variant: 'link', isDestructive: true, onClick: function () { removeStep( idx ); } }, 'Entfernen' )
							);
						} ),
						el( Button, { variant: 'secondary', onClick: addStep }, 'Schritt hinzufügen' )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/join-timeline', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
