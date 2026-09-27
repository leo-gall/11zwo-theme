( function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var RangeControl = wp.components.RangeControl;
	var ToggleControl = wp.components.ToggleControl;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType( 'elfzwo/person-card-group', {
		edit: function ( props ) {
			var a = props.attributes;
			var people = a.people || [];

			function set( key ) {
				return function ( v ) {
					var o = {};
					o[ key ] = v;
					props.setAttributes( o );
				};
			}
			function updatePerson( idx, key, value ) {
				var next = people.slice();
				var patch = {};
				patch[ key ] = value;
				next[ idx ] = Object.assign( {}, next[ idx ], patch );
				props.setAttributes( { people: next } );
			}
			function removePerson( idx ) {
				var next = people.slice();
				next.splice( idx, 1 );
				props.setAttributes( { people: next } );
			}
			function movePerson( idx, dir ) {
				var target = idx + dir;
				if ( target < 0 || target >= people.length ) { return; }
				var next = people.slice();
				var tmp = next[ idx ];
				next[ idx ] = next[ target ];
				next[ target ] = tmp;
				props.setAttributes( { people: next } );
			}
			function addPerson() {
				props.setAttributes( { people: people.concat( [ { name: '', rolle: '', telefon: '', email: '', showEmail: false } ] ) } );
			}

			return el(
				'div', {},
				el(
					InspectorControls,
					{},
					el( PanelBody, { title: 'Ansprechpartner-Gruppe' },
						el( TextControl, { label: 'Gruppentitel', value: a.title, onChange: set( 'title' ) } ),
						el( RangeControl, { label: 'Spalten', min: 1, max: 4, value: a.columns, onChange: set( 'columns' ) } )
					),
					el( PanelBody, { title: 'Ansprechpartner', initialOpen: true },
						people.map( function ( person, idx ) {
							return el(
								'div',
								{ key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el( TextControl, { label: 'Name', value: person.name, onChange: function ( v ) { updatePerson( idx, 'name', v ); } } ),
								el( TextControl, { label: 'Rolle', value: person.rolle, onChange: function ( v ) { updatePerson( idx, 'rolle', v ); } } ),
								el( TextControl, { label: 'Telefon', value: person.telefon, onChange: function ( v ) { updatePerson( idx, 'telefon', v ); } } ),
								el( TextControl, { label: 'E-Mail', value: person.email, onChange: function ( v ) { updatePerson( idx, 'email', v ); } } ),
								el( ToggleControl, { label: 'E-Mail anzeigen', checked: !! person.showEmail, onChange: function ( v ) { updatePerson( idx, 'showEmail', v ); } } ),
								el( Button, { variant: 'link', onClick: function () { movePerson( idx, -1 ); }, disabled: idx === 0 }, '↑ Hoch' ),
								el( Button, { variant: 'link', onClick: function () { movePerson( idx, 1 ); }, disabled: idx === people.length - 1 }, '↓ Runter' ),
								el( Button, { variant: 'link', isDestructive: true, onClick: function () { removePerson( idx ); } }, 'Entfernen' )
							);
						} ),
						el( Button, { variant: 'secondary', onClick: addPerson }, 'Ansprechpartner hinzufügen' )
					)
				),
				el( ServerSideRender, { block: 'elfzwo/person-card-group', attributes: a } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )();
