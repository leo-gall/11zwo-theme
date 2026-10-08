(function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType('elfzwo/mitmachen-form', {
		edit: function (props) {
			var a = props.attributes;
			var interests = a.interests || [];

			function updateItem(idx, key, value) {
				var next = interests.slice();
				var item = Object.assign({}, next[idx]);
				item[key] = value;
				next[idx] = item;
				props.setAttributes({ interests: next });
			}
			function removeItem(idx) {
				var next = interests.slice();
				next.splice(idx, 1);
				props.setAttributes({ interests: next });
			}

			return el(
				'div', {},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: 'Text' },
						el(TextControl, { label: 'Überschrift', value: a.title, onChange: function (v) { props.setAttributes({ title: v }); } }),
						el(wp.components.TextareaControl, { label: 'Text unter der Überschrift', value: a.text, rows: 3, onChange: function (v) { props.setAttributes({ text: v }); } })
					),
					el(
						PanelBody,
						{ title: 'Auswahlmöglichkeiten' },
						el('p', { style: { color: '#757575' } }, 'Anfragen landen im Backend unter „Anfragen“.'),
						interests.map(function (item, idx) {
							return el(
								'div',
								{ key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el(TextControl, { label: 'Auswahl ' + (idx + 1), value: item.label || '', onChange: function (v) { updateItem(idx, 'label', v); } }),
								el(Button, { variant: 'link', isDestructive: true, onClick: function () { removeItem(idx); } }, 'Entfernen')
							);
						}),
						el(Button, { variant: 'secondary', onClick: function () { props.setAttributes({ interests: interests.concat([{ label: '' }]) }); } }, 'Auswahl hinzufügen')
					)
				),
				el(ServerSideRender, { block: 'elfzwo/mitmachen-form', attributes: a })
			);
		},
		save: function () {
			return null;
		},
	});
})();
