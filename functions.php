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
 * @param string $table Name of the database table
 * @return mixed Array of settings or false
 */
function haendlersuche_get_settings($table) {
	global $wpdb;
	
	if (!empty($table)) {
		return $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . $table . "`");
	}
	return false;
}

/**
 * Updates setting from given table.
 * 
 * @param array $settings Array of settings to be updated
 * @param string $table Name of the database table
 */
function haendlersuche_update_settings($settings, $table) {
	global $wpdb;

	$res = true;
	foreach ((array) $settings as $key => $value) {
		$updateRes = $wpdb->update($wpdb->prefix . $table, ['value' => $value], ['name' => $key]);
		if ($updateRes === false) {
			$res = false;
		}
	}
	
	return $res;
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
