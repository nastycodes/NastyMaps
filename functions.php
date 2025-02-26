<?php
/**
 * Functions
 * 
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

/**
 * Gets the penultimate key of an array.
 * 
 * @param array $array The input array
 * @return mixed The penultimate key or null if it doesn't exist
 */
function array_key_penultimate($array) {
	if (!is_array($array) || count($array) < 2) {
		return null;
	}
	$keys = array_keys($array);
	return $keys[count($keys) - 2];
}

/**
 * Extracts the value of a key from an array and unsets it.
 * 
 * @param array $array The input array
 * @param string $key The key to extract
 * @return mixed The value of the key or null if it doesn't exist
 */
function array_extract_key(&$array, $key) {
	if (!is_array($array) || !array_key_exists($key, $array)) {
		return null;
	}
	$value = $array[$key];
	unset($array[$key]);
	return $value;
}

/**
 * Gets image-url for single/multiple image-id's
 * 
 * @param int|array $img image-id, or array of image id's.
 * @return string|array	image-url if int, otherwise array of strings
 */
function nastymaps_get_image($img) {
	if (is_array($img)) {
		return array_map(function($img){
			return wp_get_attachment_image_src($img, 'full')[0];
		}, $img);
	} else {
		return wp_get_attachment_image_src($img, 'full')[0];
	}
}

/**
 * Generates random id.
 * 
 * @param string $len length of id
 * @return string generated id
 */
function nastymaps_generate_id($len) {
	$id = '';
	for ($i = 0; $i < $len; $i++) {
		$id .= '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'[rand(0, 61)];
	}
	return $id;
}

/**
 * Gets value inbetween a value.
 * 
 * @param string $val Value to be trimmed
 * @param string $begin Value to start trimming from
 * @param string $end Value to end trimming on
 * @return string Trimmed value if possible, otherwise ''
 */
function nastymaps_get_between($val, $begin, $end) {
	return (isset(explode($begin, $val)[1]) ? explode($end, explode($begin, $val)[1])[0] : '');
}

/**
 * Gets all settings from given table.
 * 
 * @return mixed Array of settings or false
 */
function nastymaps_get_settings() {
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "nastymaps_setting`");
}

/**
 * Updates setting from given table.
 * 
 * @param array $settings Array of settings to be updated
 * @return bool True if all settings are updated, otherwise false
 */
function nastymaps_update_settings($settings) {
	global $wpdb;

	$res = true;
	foreach ((array) $settings as $key => $value) {
		$updateRes = $wpdb->update($wpdb->prefix . "nastymaps_setting", ['value' => $value], ['name' => $key]);
		if ($updateRes === false) {
			$res = false;
		}
	}
	
	return $res;
}

/**
 * Gets all metaboxes
 * 
 * @return mixed Array of metaboxes or false
 */
function nastymaps_get_metaboxes() {
	global $wpdb;
	
	return $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "nastymaps_metabox`");
}

/**
 * Adds a metabox
 * 
 * @param array $metabox Array of metabox data
 * @return bool True if metabox is added, otherwise false
 */
function nastymaps_add_metabox($metabox) {
	global $wpdb;
	
	return $wpdb->insert($wpdb->prefix . "nastymaps_metabox", $metabox);
}

/**
 * Edits a metabox
 * 
 * @param array $metabox Array of metabox data
 * @return bool True if metabox is edited, otherwise false
 */
function nastymaps_edit_metabox($metabox) {
	global $wpdb;
	
	return $wpdb->update($wpdb->prefix . "nastymaps_metabox", $metabox, ['id' => $metabox['id']]);
}

/**
 * Deletes a metabox
 * 
 * @param int $id ID of the metabox
 * @return bool True if metabox is deleted, otherwise false
 */
function nastymaps_delete_metabox($id) {
	global $wpdb;
	
	return $wpdb->delete($wpdb->prefix . "nastymaps_metabox", ['id' => $id]);
}

/**
 * Gets all maps
 * 
 * @return mixed Array of maps or false
 */
function nastymaps_get_maps() {
	global $wpdb;
	
	$maps = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "nastymaps_map`");
	if (is_array($maps) && count($maps) > 0) {
		foreach ((array) $maps as $map) {
			$map->settings = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "nastymaps_map_setting` WHERE map_id = " . $map->id);
		}
	}
	return $maps;
}

/**
 * Adds a map
 * 
 * @param array $map Array of map data
 * @return bool True if map is added, otherwise false
 */
function nastymaps_add_map($map) {
	global $wpdb;
	
	$wpdb->insert($wpdb->prefix . "nastymaps_map", $map);
	$map_id = $wpdb->insert_id;
	
	if (isset($map['settings']) && is_array($map['settings']) && count($map['settings']) > 0) {
		foreach ((array) $map['settings'] as $setting) {
			$setting['map_id'] = $map_id;
			$wpdb->insert($wpdb->prefix . "nastymaps_map_setting", $setting);
		}
	}
	
	return true;
}

/**
 * Edits a map
 * 
 * @param array $map Array of map data
 * @return bool True if map is edited, otherwise false
 */
function nastymaps_edit_map($map) {
	global $wpdb;
	
	$wpdb->update($wpdb->prefix . "nastymaps_map", $map, ['id' => $map['id']]);
	
	if (isset($map['settings']) && is_array($map['settings']) && count($map['settings']) > 0) {
		foreach ((array) $map['settings'] as $setting) {
			$wpdb->update($wpdb->prefix . "nastymaps_map_setting", $setting, ['id' => $setting['id']]);
		}
	}
	
	return true;
}

/**
 * Deletes a map
 * 
 * @param int $id ID of the map
 * @return bool True if map is deleted, otherwise false
 */
function nastymaps_delete_map($id) {
	global $wpdb;
	
	$wpdb->delete($wpdb->prefix . "nastymaps_map_setting", ['map_id' => $id]);
	$wpdb->delete($wpdb->prefix . "nastymaps_map", ['id' => $id]);
	
	return true;
}

/**
 * Gets all templates
 * 
 * @return mixed Array of templates or false
 */
function nastymaps_get_templates() {
	global $wpdb;
	
	return $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "nastymaps_template`");
}

/**
 * Adds a template
 * 
 * @param array $template Array of template data
 * @return bool True if template is added, otherwise false
 */
function nastymaps_add_template($template) {
	global $wpdb;
	
	return $wpdb->insert($wpdb->prefix . "nastymaps_template", $template);
}

/**
 * Edits a template
 * 
 * @param array $template Array of template data
 * @return bool True if template is edited, otherwise false
 */
function nastymaps_edit_template($template) {
	global $wpdb;
	
	$template['location_html'] = wp_unslash($template['location_html']);
	$template['location_css'] = wp_unslash($template['location_css']);
	$template['location_js'] = wp_unslash($template['location_js']);
	$template['map_html'] = wp_unslash($template['map_html']);
	$template['map_css'] = wp_unslash($template['map_css']);
	$template['map_js'] = wp_unslash($template['map_js']);

	return $wpdb->update($wpdb->prefix . "nastymaps_template", $template, ['id' => $template['id']]);
}

/**
 * Deletes a template
 * 
 * @param int $id ID of the template
 * @return bool True if template is deleted, otherwise false
 */
function nastymaps_delete_template($id) {
	global $wpdb;
	
	return $wpdb->delete($wpdb->prefix . "nastymaps_template", ['id' => $id]);
}

/**
 * Gets all custom fields
 * 
 * @param int optional $metabox_id ID of the metabox
 * @return mixed Array of custom fields or false
 */
function nastymaps_get_custom_fields($id = null) {
	global $wpdb;
	
	if ($id !== null) {
		return $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "nastymaps_custom_field` WHERE metabox_id = " . $id);
	}
	return $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "nastymaps_custom_field`");
}

/**
 * Adds a custom field
 * 
 * @param array $custom_field Array of custom field data
 * @return bool True if custom field is added, otherwise false
 */
function nastymaps_add_custom_field($custom_field) {
	global $wpdb;
	
	return $wpdb->insert($wpdb->prefix . "nastymaps_custom_field", $custom_field);
}

/**
 * Edits a custom field
 * 
 * @param array $custom_field Array of custom field data
 * @return bool True if custom field is edited, otherwise false
 */
function nastymaps_edit_custom_field($custom_field) {
	global $wpdb;
	
	return $wpdb->update($wpdb->prefix . "nastymaps_custom_field", $custom_field, ['id' => $custom_field['id']]);
}

/**
 * Deletes a custom field
 * 
 * @param int $id ID of the custom field
 * @return bool True if custom field is deleted, otherwise false
 */
function nastymaps_delete_custom_field($id) {
	global $wpdb;
	
	return $wpdb->delete($wpdb->prefix . "nastymaps_custom_field", ['id' => $id]);
}

/**
 * Gets all variables
 * 
 * @return mixed Array of variables or false
 */
function nastymaps_get_variables() {
	global $wpdb;
	
	return $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "nastymaps_variable`");
}

/**
 * Adds a variable
 * 
 * @param array $variable Array of variable data
 * @return bool True if variable is added, otherwise false
 */
function nastymaps_add_variable($variable) {
	global $wpdb;
	
	return $wpdb->insert($wpdb->prefix . "nastymaps_variable", $variable);
}

/**
 * Edits a variable
 * 
 * @param array $variable Array of variable data
 * @return bool True if variable is edited, otherwise false
 */
function nastymaps_edit_variable($variable) {
	global $wpdb;
	
	return $wpdb->update($wpdb->prefix . "nastymaps_variable", $variable, ['id' => $variable['id']]);
}

/**
 * Deletes a variable
 * 
 * @param int $id ID of the variable
 * @return bool True if variable is deleted, otherwise false
 */
function nastymaps_delete_variable($id) {
	global $wpdb;
	
	return $wpdb->delete($wpdb->prefix . "nastymaps_variable", ['id' => $id]);
}

/**
 * Prevent direct calls to ajax file.
 * 
 * @return void
 */
function nastymaps_is_ajax_request() {
	if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) die('What are you trying?');
}

// Loaded
define('NASTYMAPS_FUNCTIONS_LOADED', true);
?>
