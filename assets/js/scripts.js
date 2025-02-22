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
	
			// toast.addClass('hide');
	
			setTimeout(function() {
				// toast.remove();
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
	init_editor(identifier, options = {}) {
		let defaultOptions = {
			lineNumbers: true,
			mode: 'javascript',
			theme: 'vscode-dark',
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
});
