(function () {
	var el = wp.element.createElement;
	var ServerSideRender = window.elfzwoBlocks.ServerSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType('elfzwo/mitmachen-form', {
		edit: function (props) {
			var a = props.attributes;
			var interests = a.interests || [];
			var pages = wp.data.useSelect(function (select) {
				return select('core').getEntityRecords('postType', 'page', { per_page: -1, status: 'publish', orderby: 'title', order: 'asc' });
			}, []);

			function updateItem(idx, key, value) {
				var next = interests.slice();
				next[idx] = Object.assign({}, next[idx], (function () {
					var o = {};
					o[key] = value;
					return o;
				})());
				props.setAttributes({ interests: next });
			}
			function removeItem(idx) {
				var next = interests.slice();
				next.splice(idx, 1);
				props.setAttributes({ interests: next });
			}
			function addItem() {
				props.setAttributes({ interests: interests.concat([{ label: '', hint: '' }]) });
			}
			function set(key) {
				return function (v) {
					var o = {};
					o[key] = v;
					props.setAttributes(o);
				};
			}
			var steps = a.steps || [];
			function updateStep(idx, key, value) {
				var next = steps.slice();
				next[idx] = Object.assign({}, next[idx]);
				next[idx][key] = value;
				props.setAttributes({ steps: next });
			}
			function removeStep(idx) {
				var next = steps.slice();
				next.splice(idx, 1);
				props.setAttributes({ steps: next });
			}
			function addStep() {
				props.setAttributes({ steps: steps.concat([{ title: '', text: '' }]) });
			}

			return el(
				'div', {},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: 'Kopfbereich' },
						el(TextControl, { label: 'Kicker (handschriftlich)', value: a.kicker, onChange: set('kicker') }),
						el(TextControl, { label: 'Titel', value: a.title, onChange: set('title') }),
						el(TextControl, { label: 'Titel Teil 2 (rot)', value: a.titleHand, onChange: set('titleHand') }),
						el(TextareaControl, { label: 'Beschreibung', value: a.description, onChange: set('description') })
					),
					el(
						PanelBody,
						{ title: 'Datenschutz', initialOpen: false },
						el(SelectControl, {
							label: 'Link zur Datenschutzerklärung',
							help: 'Wird unter dem Absenden-Button verlinkt.',
							value: String(a.datenschutzPageId || 0),
							options: [{ value: '0', label: pages ? 'Standard (/datenschutzerklaerung/)' : 'Seiten werden geladen …' }].concat(
								(pages || []).map(function (page) {
									return { value: String(page.id), label: page.title.rendered || '(ohne Titel)' };
								})
							),
							onChange: function (v) { props.setAttributes({ datenschutzPageId: parseInt(v, 10) || 0 }); },
						})
					),
					el(
						PanelBody,
						{ title: 'So geht\'s weiter', initialOpen: false },
						steps.map(function (step, idx) {
							return el(
								'div',
								{ key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el(TextControl, { label: 'Schritt ' + (idx + 1), value: step.title, onChange: function (v) { updateStep(idx, 'title', v); } }),
								el(TextareaControl, { label: 'Text', value: step.text, rows: 2, onChange: function (v) { updateStep(idx, 'text', v); } }),
								el(Button, { variant: 'link', isDestructive: true, onClick: function () { removeStep(idx); } }, 'Entfernen')
							);
						}),
						el(Button, { variant: 'secondary', onClick: addStep }, 'Schritt hinzufügen')
					),
					el(
						PanelBody,
						{ title: 'Auswahlmöglichkeiten', initialOpen: false },
						interests.map(function (item, idx) {
							return el(
								'div',
								{ key: idx, style: { marginBottom: '16px', paddingBottom: '12px', borderBottom: '1px solid #ddd' } },
								el(TextControl, { label: 'Label ' + (idx + 1), value: item.label, onChange: function (v) { updateItem(idx, 'label', v); } }),
								el(TextControl, { label: 'Hinweis', value: item.hint, onChange: function (v) { updateItem(idx, 'hint', v); } }),
								el(Button, { variant: 'link', isDestructive: true, onClick: function () { removeItem(idx); } }, 'Entfernen')
							);
						}),
						el(Button, { variant: 'secondary', onClick: addItem }, 'Option hinzufügen')
					),
					el(
						PanelBody,
						{ title: 'Benachrichtigung', initialOpen: true },
						el(TextControl, {
							label: 'Benachrichtigungs-E-Mail',
							help: 'Bekommt bei jeder neuen Anfrage eine Mail. Mehrere Adressen mit Komma trennen. Leer = Admin-E-Mail der Website. Alle Anfragen stehen zusätzlich im Backend unter „Mitmachen-Anfragen“.',
							value: a.notifyEmail || '',
							onChange: set('notifyEmail'),
						})
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
