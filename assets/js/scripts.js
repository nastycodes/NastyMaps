/**
 * Copyright (C) 2025 nasty.codes
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

;(function(window, $) {
	"use strict";
  
	var defaultConfig = {
		type: '',
		container: '#toasts',
		autoDismiss: false,
		autoDismissDelay: 4000,
		transitionDuration: 500
	};
  
	$.toast = function(config) {
		var size = arguments.length, isString = typeof(config) === 'string';
		
		if (isString && size === 1) {
			config = {
				message: config
			};
		} else if (isString && size === 2) {
			config = {
				message: arguments[1],
				type: arguments[0]
			};
		}
		
		return new toast(config);
	};
  
	var toast = function(config) {
		config = $.extend({}, defaultConfig, config);

		var close = config.autoDismiss ? '' : '&times;';
		
		var toast = $([
			`<div class="nasty-toast ${config.type}">`,
			`<p>${config.message}</p>`,
			`<div class="close">${close}</div>`,
			`</div>`
		].join(''));
		
		toast.find('.close').on('click', function() {
			var toast = $(this).parent();
	
			toast.addClass('hide');
	
			setTimeout(function() {
				toast.remove();
			}, config.transitionDuration);
		});
		
		$(config.container).append(toast);
		
		setTimeout(function() {
			toast.addClass('show');
		}, config.transitionDuration);
	
		if (config.autoDismiss) {
			setTimeout(function() {
				toast.find('.close').click();
			}, config.autoDismissDelay);
		}
	
		return this;
	};
})(window, jQuery);

function htmlEntities(str) {
	return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

class NastyMaps {
	constructor() {
		this.templatePreviewStructure = `<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>{{ title }}</title><style>body{display:flex;justify-content:center;align-items:center;width: 100%;height: 100%;margin: 0;overflow-x:hidden;--color: #e6e6e6;background-color: #fcfcfc;background-image:linear-gradient(0deg, transparent 24%, var(--color) 25%, var(--color) 26%, transparent 27%,transparent 74%, var(--color) 75%, var(--color) 76%, transparent 77%,transparent),linear-gradient(90deg, transparent 24%, var(--color) 25%, var(--color) 26%, transparent 27%,transparent 74%, var(--color) 75%, var(--color) 76%, transparent 77%,transparent);background-size: 50px 50px;}{{ css }}</style></head><body>{{ html }}<script>{{ js }}</script></body></html>`;
	}

	init_editor(identifier, options = {}) {
		let defaultOptions = {
			lineNumbers: true,
			mode: 'javascript',
			/* theme: 'vscode-dark', */
			autoCloseBrackets: true,
			autoCloseTags: true,
			matchBrackets: true,
			matchTags: true,
			indentUnit: 4,
			minLines: 10,
			indentWithTabs: true,
			styleActiveLine: true,
			continueComments: 'Enter',
			extraKeys: {
				'Ctrl-Space': 'autocomplete',
				'Ctrl-Enter': function(cm) {
					cm.save();
				},
				'F11': function(cm) {
					cm.setOption("fullScreen", !cm.getOption("fullScreen"));
				},
				'Esc': function(cm) {
					if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
				}
			}
		};
		options = Object.assign(defaultOptions, options);

		let editor = CodeMirror.fromTextArea($(identifier)[0], options);
		editor.setSize('100%', 'auto');

		// Update the textarea on change
		editor.on('change', function(cm) {
			cm.save();
		});

		return editor;
	}

	fallback_copy(text) {
		var textArea = document.createElement("textarea");
		textArea.value = text;

		textArea.style.top = "0";
		textArea.style.left = "0";
		textArea.style.position = "fixed";

		document.body.appendChild(textArea);
		textArea.focus();
		textArea.select();

		try {
			document.execCommand('copy');
		} catch (err) {
			console.error('Fallback: Oops, unable to copy', err);
		}

		document.body.removeChild(textArea);
	}
	copy(text, success = 'Erfolgreich kopiert!', error = 'Fehler beim Kopieren!') {
		if (!navigator.clipboard) {
			this.fallback_copy(text);
			return;
		}
		navigator.clipboard.writeText(text).then(function() {
			$.toast({
				autoDismiss: true,
				type: 'success',
				message: `${success}`
			});
		}, function(err) {
			$.toast({
				autoDismiss: true,
				type: 'danger',
				message: `${error}`
			});
		});
	}
}

jQuery(document).ready(function() {
	let nastymaps = new NastyMaps();
	window.nastymaps = nastymaps;

	// Tables
	if ($("#templates-table").length > 0) {
		$("#templates-table").DataTable({
			"order": [], // Disable initial sorting
			"lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]], // Change the number of items per page
			language: {
				lengthMenu: `<div class="d-flex align-items-center justify-content-center me-3" style="height: 30px;"><img src="${$("#templates-table").data('templates-icon')}" height="20px" alt="Templates" class="icon" /></div> _MENU_`,
				info: "<p class=\"mb-0\">Showing <i class=\"color-nastymaps\">_START_</i> to <i class=\"color-nastymaps\">_END_</i> of <i class=\"color-nastymaps\">_TOTAL_</i> templates</p>",
				infoEmpty: "<p class=\"mb-0\">Showing <i class=\"color-nastymaps\">0</i> of <i class=\"color-nastymaps\">0</i> templates</p>",
				infoFiltered: "<p class=\"mb-0 text-muted\">(filtered from <i class=\"color-nastymaps\">_MAX_</i> total)</p>",
				search: `<div class="d-flex align-items-center justify-content-center" style="height: 30px;"><img src="${$("#templates-table").data('search-icon')}" height="20px" alt="Search" class="icon" /></div>&nbsp;`
			},
		});
	}

	if ($("#extensions-table").length > 0) {
		$("#extensions-table").DataTable({
			"order": [], // Disable initial sorting
			"lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]], // Change the number of items per page
			language: {
				lengthMenu: `<div class="d-flex align-items-center justify-content-center me-3" style="height: 30px;"><img src="${$("#extensions-table").data('extensions-icon')}" height="20px" alt="Extensions" class="icon" /></div> _MENU_`,
				info: "<p class=\"mb-0\">Showing <i class=\"color-nastymaps\">_START_</i> to <i class=\"color-nastymaps\">_END_</i> of <i class=\"color-nastymaps\">_TOTAL_</i> extensions</p>",
				infoEmpty: "<p class=\"mb-0\">Showing <i class=\"color-nastymaps\">0</i> of <i class=\"color-nastymaps\">0</i> extensions</p>",
				infoFiltered: "<p class=\"mb-0 text-muted\">(filtered from <i class=\"color-nastymaps\">_MAX_</i> total)</p>",
				search: `<div class="d-flex align-items-center justify-content-center" style="height: 30px;"><img src="${$("#extensions-table").data('search-icon')}" height="20px" alt="Search" class="icon" /></div>&nbsp;`
			},
		});
	}

	if ($("#settings-metaboxes-table").length > 0) {
		$("#settings-metaboxes-table").DataTable({
			"order": [], // Disable initial sorting
			"lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]], // Change the number of items per page
			language: {
				lengthMenu: `<div class="d-flex align-items-center justify-content-center me-3" style="height: 30px;"><img src="${$("#settings-metaboxes-table").data('metabox-icon')}" height="20px" alt="Metaboxes" class="icon" /></div> _MENU_`,
				info: "<p class=\"mb-0\">Showing <i class=\"color-nastymaps\">_START_</i> to <i class=\"color-nastymaps\">_END_</i> of <i class=\"color-nastymaps\">_TOTAL_</i> metaboxes</p>",
				infoEmpty: "<p class=\"mb-0\">Showing <i class=\"color-nastymaps\">0</i> of <i class=\"color-nastymaps\">0</i> metaboxes</p>",
				infoFiltered: "<p class=\"mb-0 text-muted\">(filtered from <i class=\"color-nastymaps\">_MAX_</i> total)</p>",
				search: `<div class="d-flex align-items-center justify-content-center" style="height: 30px;"><img src="${$("#settings-metaboxes-table").data('search-icon')}" height="20px" alt="Search" class="icon" /></div>&nbsp;`
			},
		});
	}

	if ($("#settings-custom-fields-table").length > 0) {
		$("#settings-custom-fields-table").DataTable({
			"order": [], // Disable initial sorting
			"lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]], // Change the number of items per page
			language: {
				lengthMenu: `<div class="d-flex align-items-center justify-content-center me-3" style="height: 30px;"><img src="${$("#settings-custom-fields-table").data('custom-field-icon')}" height="20px" alt="Custom fields" class="icon" /></div> _MENU_`,
				info: "<p class=\"mb-0\">Showing <i class=\"color-nastymaps\">_START_</i> to <i class=\"color-nastymaps\">_END_</i> of <i class=\"color-nastymaps\">_TOTAL_</i> custom fields</p>",
				infoEmpty: "<p class=\"mb-0\">Showing <i class=\"color-nastymaps\">0</i> of <i class=\"color-nastymaps\">0</i> custom fields</p>",
				infoFiltered: "<p class=\"mb-0 text-muted\">(filtered from <i class=\"color-nastymaps\">_MAX_</i> total)</p>",
				search: `<div class="d-flex align-items-center justify-content-center" style="height: 30px;"><img src="${$("#settings-custom-fields-table").data('search-icon')}" height="20px" alt="Search" class="icon" /></div>&nbsp;`
			},
		});
	}

	if ($("#settings-variables-table").length > 0) {
		$("#settings-variables-table").DataTable({
			"order": [], // Disable initial sorting
			"lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]], // Change the number of items per page
			language: {
				lengthMenu: `<div class="d-flex align-items-center justify-content-center me-3" style="height: 30px;"><img src="${$("#settings-variables-table").data('variables-icon')}" height="20px" alt="Variables" class="icon" /></div> _MENU_`,
				info: "<p class=\"mb-0\">Showing <i class=\"color-nastymaps\">_START_</i> to <i class=\"color-nastymaps\">_END_</i> of <i class=\"color-nastymaps\">_TOTAL_</i> variables</p>",
				infoEmpty: "<p class=\"mb-0\">Showing <i class=\"color-nastymaps\">0</i> of <i class=\"color-nastymaps\">0</i> variables</p>",
				infoFiltered: "<p class=\"mb-0 text-muted\">(filtered from <i class=\"color-nastymaps\">_MAX_</i> total)</p>",
				search: `<div class="d-flex align-items-center justify-content-center" style="height: 30px;"><img src="${$("#settings-variables-table ").data('search-icon')}" height="20px" alt="Search" class="icon" /></div>&nbsp;`
			},
		});
	}


	if ($(".toggle-icon-button").length > 0) {
		$('.toggle-icon-button').each((i, button) => {
			$(button).on('click', () => {
				$(button).find('.icon-expand').toggleClass('d-none');
				$(button).find('.icon-collapse').toggleClass('d-none');
				if ($(button).find('p')) {
					let buttonTextElement = $(button).find('p');
					let buttonText = $(buttonTextElement).text();
					$(button).find('p').text(buttonText === $(buttonTextElement).attr('data-collapse-text') ? $(buttonTextElement).attr('data-expand-text') : $(buttonTextElement).attr('data-collapse-text'));
				}
			});
		});
	}

	if ($(".nastymaps-editor").length > 0) {
		$(".nastymaps-editor").each((i, editor) => {
			let config = {
				mode: $(editor).data('mode'),
			};

			if ($(editor).closest('.modal').length > 0) {
				let parentModal = document.getElementById($(editor).closest('.modal').attr('id'));
				parentModal.addEventListener('shown.bs.modal', event => {
					if ($(editor).attr('data-initialized') === 'true') return;

					let editorInstance = nastymaps.init_editor(`#${$(editor).attr('id')}`, config);
					$(editor).attr('data-initialized', 'true');
				});
			} else {
				if ($(editor).attr('data-initialized') === 'true') return;
				let editorInstance = nastymaps.init_editor(`#${$(editor).attr('id')}`, config);
				$(editor).attr('data-initialized', 'true');
			}
		});
	}

	if ($(".variable-btn").length > 0) {
		$(".variable-btn").each((i, button) => {
			$(button).on('click', (e) => {
				e.preventDefault();
				let variable = $(button).data('variable');
				let name = $(button).data('name');
				// copy to clipboard
				nastymaps.copy(variable, `Successfully copied <b>${name}</b>`, `Failed to copy <b>${name}</b>!`);
			});
		});
	}

	if ($(".preview-refresh-btn").length > 0) {
		$(".preview-refresh-btn").each((i, button) => {
			$(button).on('click', () => {
				let icon = $(button).find('img');
				if (icon.length > 0 && !icon.hasClass('spin')) {
					icon.addClass('spin');
					setTimeout(() => {
						icon.removeClass('spin');
					}, 1000);
				} else if (icon.length > 0) {
					icon.removeClass('spin');
				}

				if ($(`#${$(button).data('preview-selector')}`).length > 0) {
					let css = $(`#${$(button).data('css-selector')}`).val();
					let html = $(`#${$(button).data('html-selector')}`).val();
					let js = $(`#${$(button).data('js-selector')}`).val();
					$(`#${$(button).data('preview-selector')}`).attr('srcdoc', nastymaps.templatePreviewStructure.replace('{{ title }}', 'Preview').replace('{{ css }}', css).replace('{{ html }}', html).replace('{{ js }}', js));
				}
			});
			if ($(`#${$(button).data('preview-selector')}`).length > 0) {
				let css = $(`#${$(button).data('css-selector')}`).val();
				let html = $(`#${$(button).data('html-selector')}`).val();
				let js = $(`#${$(button).data('js-selector')}`).val();
				$(`#${$(button).data('preview-selector')}`).attr('srcdoc', nastymaps.templatePreviewStructure.replace('{{ title }}', 'Preview').replace('{{ css }}', css).replace('{{ html }}', html).replace('{{ js }}', js));
			}
		});
	}
	/* 
	if ($("#template-add-location-preview-refresh").length > 0) {
		$("#template-add-location-preview-refresh").on('click', () => {
			let icon = $("#template-add-location-preview-refresh").find('img');
			if (icon.length > 0 && !icon.hasClass('spin')) {
				icon.addClass('spin');
				setTimeout(() => {
					icon.removeClass('spin');
				}, 1000);
			} else if (icon.length > 0) {
				icon.removeClass('spin');
			}

			let css = $("#template-add-location-css").val();
			let html = $("#template-add-location-html").val();
			let js = $("#template-add-location-js").val(); 
			$("#template-add-location-preview").attr('srcdoc', nastymaps.templatePreviewStructure.replace('{{ title }}', 'Location preview').replace('{{ css }}', css).replace('{{ html }}', html).replace('{{ js }}', js));
		});
	}
	if ($("#template-add-location-preview").length > 0) {
		let css = $("#template-add-location-css").val();
		let html = $("#template-add-location-html").val();
		let js = $("#template-add-location-js").val(); 
		$("#template-add-location-preview").attr('srcdoc', nastymaps.templatePreviewStructure.replace('{{ title }}', 'Location preview').replace('{{ css }}', css).replace('{{ html }}', html).replace('{{ js }}', js));
	} */
});
